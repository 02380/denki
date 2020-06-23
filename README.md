# denki: flashback to php

denki is a flexible little php content management system designed for extremely simple and basic web deployment. It's designed to load pages quickly and that's about it.

powered by parsedown and php.

## Deployment

Download this repo and get started. I personally like using `degit` for stuff like this.

To deploy safely, put the `index.php` into your public_html folder and the denki folder in the directory level below it. This is to help stop assets being directly accessible by end-users. Though it is NOT foolproof!

If you want to run it ALL in one folder, merge the denki and html folders. Denki autodetects whether this merge has happened on setup.

## Professional?

No, not at all. There will be holes in this, but it's designed to be built on top of. There are no guarantees!