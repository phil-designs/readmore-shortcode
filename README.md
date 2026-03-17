# Lightweight Read More Shortcode

Tags: jQuery, javascript, php
Requires at least: 3.6.0
Tested up to: 6.9.1
License: GPL2

## Description

Wrap any visual editor content in [readmore] to collapse/expand it with "Read More" / "Close" links. Supports multiple instances per page.

A simple count up on scroll plugin.

## Tested on 
* Firefox 
* Safari
* Chrome
* Opera
* MS Edge

## Website 
http://www.phildesigns.com/

## Installation 
1. Upload ‘readmore-shortcode’ to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Wrap your numbers with [readmore][/readmore] shortcode tags.

All shortcode attributes (all optional)

Attribute	     Default	    Description
collapsed_height     200	    Height in px when collapsed
speed	             100	    Toggle animation speed (ms)
more_text	     Read More	    Label for the expand link
less_text	     Close	    Label for the collapse link
start_open	     false	    Start in the expanded state
height_margin	     16	            Tolerance before truncation kicks in

Example with options:

[readmore collapsed_height="300" more_text="Show more" less_text="Show less" speed="300"]
...content...
[/readmore]

## Changelog 

Version 1.0.0
• Initial release.
