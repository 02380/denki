# denki: flashback to php

denki is a flexible little php content management system designed for extremely simple and basic web deployment. It's designed to load pages quickly and that's about it. I use it for the internal wikis and knowledgebases for [surge radio](http//surgeradio.co.uk) and [SUSUtv](http://susu.tv).

powered by parsedown and php.

## Features

* Fast page loading
* Very basic theming system where you have the "freedom" to write the HTML/CSS/JS any way you want.
* 100% server side, no client side functions meaning it'll work basically anywhere with PHP5.5+

## Deployment

Download this repo and get started. I personally like using `degit` for stuff like this.

To deploy safely, put the `index.php` into your public_html folder and the denki folder in the directory level below it. This is to help stop assets being directly accessible by end-users. Though it is NOT foolproof!

If you want to run it ALL in one folder, merge the denki and html folders. Denki autodetects whether this merge has happened on setup.

## Professional?

No, not at all. There will be holes in this, but it's designed to be built on top of. There are no guarantees!

## Security?

HAHAHAHAHAHAHAHAHAHAHAHAHAH

Not really again. The passwords are SHA1 hashes, remember this is because they're used in extremely low-stakes environments. I will work something more complex when I come up with an elegant modules system.