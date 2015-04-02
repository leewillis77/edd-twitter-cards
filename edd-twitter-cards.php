<?php
/*
Plugin Name: Twitter Product Cards for Easy Digital Downloads
Description: Adds twitter product card info meta tags to product pages.
Plugin URI: http://www.leewillis.co.uk/
Author: Lee Willis
Author URI: http://www.leewillis.co.uk
Version: 1.0
License: GPLv2
*/

/*
    Copyright (C) 2015 Lee Willis // www.leewillis.co.uk

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( __DIR__ . '/edd-twitter-cards.class.php' );
$edd_twitter_cards = new EddTwitterCards();