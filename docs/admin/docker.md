Installing in docker
====================

::: {.contents}
:::

Step 0: Create a volume
-----------------------

In order to have persistent data, we can create a volume to store all
the data there.

`docker volume create phpreport`

This will create a volume called [phpreport]{.title-ref}.

Step 1: Create the image
------------------------

Now we can create the image that contains phpreport.

`docker build . -t phpreport`

This will create an image called [phpreport]{.title-ref}

Step 2: Starting a container with phpreport
-------------------------------------------

Now we can start a phpreport in a container, using the previously
created volume to have persistent data.

`docker run -v phpreport:/var/lib/postgresql -p 80:80 phpreport`

After a while, open <http://localhost/phpreport> with the browser.

The default user is [admin]{.title-ref} and default password is
[admin]{.title-ref}.
