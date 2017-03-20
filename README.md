Webcam Login using Microsoft face API
============================

This webcam login is a php- and javascript- based script allowing to create Login-sessions. Your face now serves as password, which gives more security because of more individuality.
This Project works with the [Microsoft Face API](https://www.microsoft.com/cognitive-services/en-us/face-api), jQuery and [WebcamJS](https://github.com/jhuckaby/webcamjs).

# Getting started
* Clone this repository
* Create a database and insert the file '/database.sql'
* Enter your database credentials in '/oop/DatabaseConnection.class.php'
* Register to [Microsoft Face API](https://www.microsoft.com/cognitive-services/en-us/face-api) and get your individual API key
* Enter your Microsoft Face API key and settings in 'oop/FaceRecognition.class.php'
* Optional: Enable SSL for more security

# Usage examples
Check my [live version](http://jules-rau.de/projects/webcam-login) to get a feeling for it.

# Licensing
Webcam login is licensed under the MIT license
