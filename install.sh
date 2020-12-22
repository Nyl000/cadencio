
echo "
Welcome to the Cadencio Installer ! Please answer to theses questions to configure your environment.
";

echo "
API Configuration:
";

cp -p config.sample.php config.php

echo Database Host ?
read dbhost;

echo Database User ?
read dbuser;

echo Database Password ?
read dbpassword;

echo Database Name ?
read dbname;

echo  What is the base URL ? \(Please avoid trailing /\)
read baseurl;

echo  What is the API base URL ? \(Please avoid trailing /\)
read apiurl;

randomjwt=$(openssl rand -base64 32)$(openssl rand -base64 32);


sed -i'.bak' 's|%DBUSER%|'$dbuser'|g' config.php;
sed -i'.bak' 's|%DBNAME%|'$dbname'|g' config.php;
sed -i'.bak' 's|%DBPASSWORD%|'$dbpassword'|g' config.php;
sed -i'.bak' 's|%DBHOST%|'$dbhost'|g' config.php;
sed -i'.bak' 's|%APIBASEURL%|'$apiurl'|g' config.php;
sed -i'.bak' 's|%BASEURL%|'$baseurl'|g' config.php;
sed -i'.bak' "s|%JWT_KEY%|$randomjwt|g" config.php;

rm config.php.test.bak;

echo "
Running Composer Install.
";


cd src;
php composer.phar install;
cd ../


echo "
Running Db Installer.
";

cd batch;
php dbinstall.php;
cd ../


echo "
FrontEnd Configuration:
";

cd public;
cp -p config.sample.js config.js

echo  "What is the FrontEnd base URL ? (Please avoid trailing /)"
read fronturl;

echo  What is the App Name ? \(Used in title html tag\)
read appname;

sed -i'.bak' 's|%api_url%|'$apiurl'|g' config.js;
sed -i'.bak' 's|%base_url%|'$fronturl'|g' config.js;
sed -i'.bak' 's|%app_name%|'$appname'|g' config.js;

rm config.js.bak;


echo "
Running Npm Install
";

npm install


echo "
Building FrontEnd
";


#Todo: get rid of that *** htaccess as a folder..
rm dist/.htaccess 2> /dev/null
npm run build && rm -r dist/.htaccess && cp -p .htaccess.sample dist/.htaccess

echo  "

All Done !

You can change logo and favicon by replacing or by using a hook in your plugins.
 - public/resources/images/favicon.png
 - public/resources/images/logo.png
 ";

