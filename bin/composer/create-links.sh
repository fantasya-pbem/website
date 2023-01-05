#!/bin/sh

echo Creating Bootstrap CSS links...
rm -f public/css/bootstrap.*
ln -s ../../vendor/twbs/bootstrap/dist/css/bootstrap.css public/css/bootstrap.css
ln -s ../../vendor/twbs/bootstrap/dist/css/bootstrap.css.map public/css/bootstrap.css.map
ln -s ../../vendor/twbs/bootstrap/dist/css/bootstrap.min.css public/css/bootstrap.min.css
ln -s ../../vendor/twbs/bootstrap/dist/css/bootstrap.min.css.map public/css/bootstrap.min.css.map

echo Creating Bootstrap JS links...
rm -f public/js/bootstrap.*
ln -s ../../vendor/twbs/bootstrap/dist/js/bootstrap.min.js public/js/bootstrap.min.js
ln -s ../../vendor/twbs/bootstrap/dist/js/bootstrap.min.js.map public/js/bootstrap.min.js.map

echo Creating Lemuria fcheck template link...
rm -f var/check/lemuria.tpl
ln -s ../../vendor/lemuria-pbem/engine-fantasya/fcheck/lemuria.tpl var/check/lemuria.tpl
