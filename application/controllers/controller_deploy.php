<?php
/**
 * Copyright (c) 2016. Vitaliy Korenev (http://newpage.in.ua)
 */

class Controller_Deploy extends Controller
{
    function action_index()
    {
        $commands = array(
            'echo $PWD',
            'whoami',
            '/usr/local/cpanel/3rdparty/bin/git pull',
            '/usr/local/cpanel/3rdparty/bin/git status',
            '/usr/local/cpanel/3rdparty/bin/git submodule sync',
            '/usr/local/cpanel/3rdparty/bin/git submodule update',
            '/usr/local/cpanel/3rdparty/bin/git submodule status',
        );

        // Run the commands for output
        $output = '';
        foreach($commands AS $command){
            // Run it
            $tmp = shell_exec($command);
            // Output
            $output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
            $output .= htmlentities(trim($tmp)) . "\n";
        }

        ?>
        <!DOCTYPE HTML>
        <html lang="en-US">
        <head>
            <meta charset="UTF-8">
            <title>GIT DEPLOYMENT SCRIPT</title>
        </head>
        <body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
        <pre>
         .  ____  .    ____________________________
         |/      \|   |                            |
        [| <span style="color: #FF0000;">&hearts;    &hearts;</span> |]  | Git Deployment Script v0.1 |
         |___==___|  /              &copy; oodavid 2012 |
                      |____________________________|

            <?php echo $output; ?>
        </pre>
        </body>
        </html>
        <?php
    }
}