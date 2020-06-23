# denki
flexible little php content management system

## Deployment

To deploy safely, put the `index.php` into your public_html folder and the denki folder in the directory level below it. This is to help stop assets being directly accessible by end-users. Though it is NOT foolproof!

If you want to run it ALL in one folder, merge the denki and html folders. Denki autodetects whether this merge has happened on setup.