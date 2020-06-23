
<html><head>
    <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo $config["site_name"]; ?></title>
    
    <style>
    <?php
      echo file_get_contents("themes/theme.".$config['theme_name'].".css")
    ?>
    </style>
  </head>
  <body>

<header><h1><a href="./">🎵 <?php echo $config["site_name"]; ?> 🎵</a></h1></header>
    
 

    <main id="main" style="display: flex;">

    <div class="sidebar">
      <?php
        echo handle_sidebar();
      
        echo handle_sidebar_sudo();
      ?>
      <h6>designed by byron 😊</h6>
    </div>

    <div class="article_container">
      <div id="article">
          <?php
            handle_render();
          ?>
      </div>

      <div class="editbar-container">
        <ul class="editbar">
          <li class="editbar-item">Last edited: <? handle_date_meta($page); ?></li>
          <li class="editbar-item"><button class="button"><a href="?action=edit&page=<?php echo $page?>">🖊 Edit</a></button></li>
        </ul>
      </div>
    </div>
  
  </main>
</body></html>