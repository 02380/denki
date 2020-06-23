<?php
	session_start();
	require_once '../denki/vendor/autoload.php';
?>

 <?php
  $sudo_passwd = ""; // todo: add to a future config.json

  if (version_compare(PHP_VERSION, '5.5.0') <= 0) {
    die("please install on php >5.5.");
  }
  date_default_timezone_set('UTC');

  $GLOBALS["config"] = [
    "theme_name" => "default",
    "site_name" => "denki test",
    "denki_dir" => "../denki/",
    "sudo_passwd" => ""
  ];
  function get_ddir($x) {
    return $GLOBALS["config"]["denki_dir"].$x;
  }

  if(file_exists(get_ddir(".denki_init"))) {
    $x = json_decode(file_get_contents(get_ddir("config.json")),true);
    if (isset($x["denki_dir"])) {
      $GLOBALS["config"] = $x;
    }
  }

  if(file_exists("composer.json")) {
    // if running in merged folder, then auto change the denki dir to the local!
    $GLOBALS["denki_dir"] = "./";
  }

  function getDirContents($dir, &$results = array()) {
      $files = scandir($dir);

      foreach ($files as $key => $value) {
          $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
          if (!is_dir($path)) {
              $results[] = $path;
          } else if ($value != "." && $value != "..") {
              getDirContents($path, $results);
              $results[] = $path;
          }
      }

      return $results;
  }


  function handle_first_time() {
    // when you run denki on a clean install you should see thuis page.
    // set the sudo mode to true because we're in development here!
     $_SESSION['sudo']=true;
    $setup_str = <<<EOD
  <main>
    <h2>Denki setup procedure</h2>
    <p>Welcome to Denki - the barebones Markdown-powered site renderer.</p>
    <p>You need to fill in some details and set up a sudo password. There are currently no users or permissions with Denki.</p>
    <form action="?action=config&init" method="post">
    Enter your site name: <input name="config_site_name" type="text" placeholder="Site name"/><br>
    Enter your sudo password: <input name="config_sudo_passwd" type="text" placeholder="toor"/><br>
    <button type="submit">Submit</button>
    </form>
  </main>
  <style>body {-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;text-rendering: optimizeLegibility;background: #222;font-family: -apple-system, BlinkMacSystemFont, 'Helvetica Neue',Helvetica,sans-serif;margin: 0;padding: 0;}
main {
  margin-left: auto;
  margin-right: auto;
  width: 40%;
  background: #333;
  padding: 12px;
  color: white;
  font-weight: 100;
  margin-top: 12px;
  text-align:center;
}
form {
  width:100%;
  text-align:left;
}
  </style>
EOD;
    mkdir(get_ddir("pages"));
    // chmod(get_ddir("./pages/*"), 0777);
    file_put_contents(get_ddir("config.json"), "{}");
    file_put_contents(get_ddir("pages/index.md"), "Welcome to your new blank wiki!");
    file_put_contents(get_ddir("pages/sidebar.md"), "* Edit me in sudo mode!");
    die($setup_str);
  }

  if (file_exists(get_ddir("pages/index.md"))) {
    // echo handle_page($GLOBALS["page"]);
  } else {
    handle_first_time();
  }

  function handle_log($n) {
    file_put_contents(get_ddir("log.txt"), $person, FILE_APPEND | LOCK_EX);
  }

  function needs_sudo_or_else() {
    if (isset($_SESSION['sudo'])) {
      // we don't really need anything here.
    } else {
      die("You lack authorisation! Log in as sudo in order to perform this task.");
    }
  }

  function handle_page($n) {
    $Parsedown = new Parsedown();
    // $Parsedown->setMarkupEscaped(true);
    $x = file_get_contents(get_ddir("pages/{$n}.md"));
    if (substr( $x, 0, 5 ) === "SUDO!") {
      needs_sudo_or_else();
      $x = substr( $x, 5 );
    } 
    return $Parsedown->text($x);
  }

  function handle_page_plain($n) {
    $x = file_get_contents(get_ddir("pages/{$n}.md"));
    if (substr( $x, 0, 5 ) === "SUDO!") {
      needs_sudo_or_else();
      return $x;
    } else {
      return $x;
    }
  }


  function handle_render() {
    // this currently handles ALL action code which is a bit stupid.
    // TODO: Change this.
    if (isset($_SESSION['sudo'])&&isset($_GET['sudo'])) { die("You are now logged in!");}
    switch ($GLOBALS["action"]) {
        case 'config':
          needs_sudo_or_else();
          $GLOBALS["config"]["site_name"] = $_POST["config_site_name"];
          $GLOBALS["config"]["sudo_passwd"] = sha1($_POST["config_sudo_passwd"]);
          $GLOBALS["config"]["denki_dir"] = "../denki/";
          // echo json_encode($GLOBALS["config"]);
          file_put_contents(get_ddir("config.json"), json_encode($GLOBALS["config"]));
          if (isset($_GET["init"])) {
            file_put_contents(get_ddir(".denki_init"),"done");
            echo "Changes saved to the denki config! Welcome!";
          } else {
            echo "Changes saved to the denki config!";
          }
          break;
        case 'change':
          file_put_contents(get_ddir("pages/".$GLOBALS["page"].".md"), $_POST['text_to_store']);
          # code...
          if (file_exists(get_ddir("pages/".$GLOBALS["page"].".md"))) {
            echo "<meta http-equiv='refresh' content='0;url=./?view=".$GLOBALS["page"]."'>";
          } else {
            handle_death("no_file");
          }
          break;
        case 'sudo':
          needs_sudo_or_else();
          echo "<div class='sudo-pages'><h1>Sudo Page List</h1><ul>";
          foreach (getDirContents(get_ddir('./pages')) as &$value) {
            if (strpos($value, '.md') !== false) {
              $whatIWant = substr($value, strpos($value, "pages/") + 6);    
              echo "<li><a href='?action=edit&page=".substr($whatIWant, 0, -3)."'>{$whatIWant}</a></li>";
            }
              
          }
          echo "</div>";
          // echo "<div class='sudo-pages'><h1>Sudo 2</h1><ul>";
          // echo "</div>";
          break;
        case 'sudo_exit':
          session_destroy();
          die("You are now logged out. :-(");
          break;
        case 'sudo_auth':
          echo '<form class="login" method="POST" action="?sudo">
   <!-- User <input type="text" name="user"></input>--><br/>
    Enter the sudo password: <input type="password" name="pass"></input><br/>
    <input type="submit" name="submit" value="Go"></input></form>';
          break;

        case 'view':
          # code...
          if (file_exists(get_ddir("pages/".$GLOBALS["page"].".md"))) {
            echo handle_page($GLOBALS["page"]);
          } else {
            // handle_death("no_file");
            $edit_window = '<form action="?action=change&page='.$GLOBALS["page"].'" method="post"><div id="editbox" class="editbox"><textarea name="text_to_store" id="edit_data"># New PAGE!</textarea><div class="e"><ul class="editbar"><li class="editbar-item"><button type="submit" class="button">💾 Save</button></li></ul></div></div>';
              echo $edit_window;
          }
          break;

        case 'edit':
          // needs_sudo_or_else();
          if (file_exists(get_ddir("pages/".$GLOBALS["page"].".md"))){
           $edit_window = '<form action="?action=change&page='.$GLOBALS["page"].'" method="post"><div id="editbox" class="editbox"><textarea name="text_to_store" id="edit_data">'.handle_page_plain($GLOBALS["page"]).'</textarea><div class="e"><ul class="editbar"><li class="editbar-item">You are editing this page.</li><li class="editbar-item"><button type="submit" class="button">💾 Save</button></li></ul></div></div>';
            echo $edit_window;
          }
          break;
        
        default:
          handle_death("no_action");
          break;
      }
  }
  function handle_date_meta($page) {
    $x = filemtime(get_ddir("pages/".$GLOBALS["page"].".md"));
    if ($x == true) {
      echo date("F d Y H:i:s.", $x);
    }
  }

  function handle_sidebar_sudo() {
    if (isset($_SESSION["sudo"])) {
      $xx = "* [edit sidebar](?action=edit&page=sidebar)\n\n* [sudo panel](?action=sudo)\n* [logout](?action=sudo_exit)";
    } else {
      $xx = "* [🔐 sudo mode (wip)](?action=sudo_auth)";
    }
    $Parsedown = new Parsedown();
    echo $Parsedown->text($xx);
  }

  function handle_sidebar() {
    $Parsedown = new Parsedown();
    // $Parsedown->setMarkupEscaped(true);
    $x = file_get_contents(get_ddir("pages/sidebar.md"));

    return $Parsedown->text($x);
  }
  function handle_death($reason) {
    die($reason);
  }

  $json_output = [
      "success" => 0
  ];

  if (isset($_GET['gitpush'])) {
      // echo getcwd() . "\n";
      // echo system ("git add .; git commit -m 'test'; git push");
      die("git not working yet!");
  }
  if (isset($_GET['sudo'])) {
    // sudo code
    // messy but it works here
    if(isset($_POST['pass'])) {
      if (sha1($_POST['pass'])==$GLOBALS["config"]["sudo_passwd"]) {
        $_SESSION['sudo']=true;
      }
    }
  } else {
    if (isset($_GET['view'])) {
      $GLOBALS["action"] = 'view';
      $GLOBALS["page"] = $_GET['view'];
    }
    else {
      $GLOBALS["action"] = 'view';
      $GLOBALS["page"] = 'index';
      if (isset($_GET['action'])) {
          $GLOBALS["action"] = $_GET['action'];
      }
      if (isset($_GET['page'])) {
          $GLOBALS["page"] = $_GET['page'];
      }
    }
  }
  if (isset($GLOBALS["config"]["theme_name"])) {
  require(get_ddir("themes/theme.".$config['theme_name'].".php"));
  } else {
    require "page.php";
  }
?>