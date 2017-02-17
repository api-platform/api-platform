#!/bin/sh
set -xe

varnishd -a :80 -f /etc/varnish/default.vcl -s malloc,256m
varnishlog
