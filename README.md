unblu how to: Context switching for sessions secured with a “http only” cookie. 
===============================================================================


Introduction / Background:
unblu teams/performance supports in-context session migration when co-browsing is initiated. Pre-requisite for this capability is that the corresponding setting is in place in the account configuration. In unblu´s standard session migration procedure, unblu reads the session cookie through a java script activity on the visitor´s browser when the co-browsing session is initiated. The cookie values are passed to the co-browsing session and through a re-load the visitors´session state is re-established (i.e. the items already placed in a shopping cart are now also present in the co-browsing sessions). For session cookies which are secured via an http-only flag, unblu´s out of the box session migration process does not work, as unblu cannot read the cookie value through java script. This document describes the necessary additional configuration and instrumentation that needs to be in place so that session migration can also function with http-only secured session cookies. 


Additional Configuration for Session Migration with HTTP-Only Cookies:
To support the migration of sessions secured through http-only cookies, a secure and temporary bridge between the web application´s session and the unblu session needs to be established. This can be achieved with some additional steps implemented in your web application as per the steps described below. For our example, we assume that a backend session already exists (or is started) before the user requests an unblu co-browsing session. For example, in our simple sample code, the user has already clicked on the button "click to add item to your cart" so that a session cookie is created.

Steps to achieve the session migration as per sample code provided. When we refer to “server” in this example, the instrument web application is meant. 
1. on request of a co-browsing session (user clicks on "Click Here For Live Support"), client sends an AJAX request to the server and creates the bridge 
2. the server creates a temporary cookie with no http-only flag; this cookie has a short expiration time to maintain security (i.e. 2 minutes).
3. the cookie value has the encrypted value of the original session cookie.
4. the server then sends a 200 status code to the client.
5. as soon as the client gets the success response, it calls the "onSuccess" callback, which creates the unblu dialog to start a session.
6. the unblu server always sends a cookie with the response header that carries the name "x-unblu-account-secret”; the value of that cookie is your account specific secret key.
7. on server side you have a check for session migration;  use the bridge you created in step 2 and check for the "x-unblu-account-secret". (done in utils/migrate.php)
 a. the server checks whether the bridge exists, if yes:
     a1. its value is read and decrypted.
     a2. the decrypted value is checked against DB/Session-Storage to see whether it is a valid session cookie.
     a3. if a-2 is true, then server checks whether your account secret key matches with the value of the cookie that comes from the unblu server.
     a4. if a-2 and a-3 is both true, then the server creates the original cookie with the value (a-1) of the http-only cookie.
     a5. the server deletes the bridge (the temporary cookie)
8. session migration is complete.  



Description of the sample implementation in PHP:
The provided sample is a simple implementation with PHP on a test page to illustrate the necessary instrumentation. The implementation contains:
1. index.php, a simple html page with only 4 elements; a title, a button to add an item to a shopping-cart, a shopping-cart and a link to start an unblu session.
2. utils/preparebridge.php, a simple PHP code element thats performs steps 2 and 4 described above.
3. utils/utils.php, which includes the scripts to handle steps 2 and 7 described above.
4. js/unbluintegration.js, includes client side scripts; steps 1 and 5 described above.
5. css/base.css, contains some simple css rules.
6. img/empty.png and img/full.png images to show the status of the shopping-cart.
7. utils/service.php, provides some service to get status of cart whether it is empty or full
8. utils/migrate.php, does the migrate, calls the corresponding functions to do the migration

Notes:
In case you are going to try the simple on your environment or on a test server, please make sure that
1. in file index.php, please remove the example Unblu snippet and add your Unblu snippet according to the Unblu version you are using.
2. in file utils/utils.php, the constants MY_COOKIE_DOMAIN and MY_ACCOUNT_SECRET_KEY are replaced/set with actual value.

This example (and all code) is a pure guide to illustrate a simple implementation of the logic required to migrate a session cookie into unblu universal session. 
The example (and all code) is not considered to be deployed on any productive environment, as stated before the example (and all code) is only and only a simple example to assist you understand the logic and steps to implement your own code.
   
