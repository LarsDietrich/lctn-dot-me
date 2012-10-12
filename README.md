lctn.me
=======

A a portal that aggregates location based information into a single place. You select a point on the planet and information is loaded relative to that point. You can then "share" this location with people via the social links supplied.

The live site can be found here: http://lctn.me

This was an experimental project allowing me to play with Location Based API's, Javascript (jQuery) and PHP.

The site makes use of the following API's:

- Google Maps (https://developers.google.com/maps/)
- Twitter (https://dev.twitter.com/)
- foursquare (https://developer.foursquare.com/)
- Instagram (http://instagram.com/developer/)
- World Weather Online (http://worldweatheronline.com/)
- Wikipedia (http://www.geonames.org/)
- Webcams.Travel (http://www.webcams.travel/developers/)


The portal is designed to run on a linux, although it should run on windows. It uses a MySQL database with a PHP / Javascript front end

The strategy with the API calls is to keep the majority of the requests client side, this works around the free limitations most API's providers have. Some of the API requests DO work via PHP requests on the server and I should probably change these to be client side only.

Installation
============

1. Setup a website 
2. Deploy the files into the websites home directory
3. Create a database for the website
4. Run the database script to initialize the database (make sure you modify the database name)
5. Setup correct values in /includes/configure.php

Check the site.

Please note, this is an experimental project on my side, I still need to do alot of cleanup and refactoring to make it of an acceptable standard.

Feedback welcome :)