=== MediaRSS ===
Contributors: andy
Tags: feed, feeds, MediaRSS, MRSS, RSS, gallery, image, images, media
Requires at least: 2.7
Tested up to: 2.7

Adds MediaRSS to your RSS2 feeds. The first image in a post will be the post's thumbnail on services that support it (e.g. FriendFeed)

== Description ==

MediaRSS is a way of embedding media into your feeds. The specification at http://search.yahoo.com/mrss/ provides for many kinds of media: audio, video, etc. This plugin is equipped to locate img tags in your posts and generate XML code that can be used by feed readers.

Also included are code samples demonstrating how to extend the plugin's functionality to meet your needs. Some PHP experience is assumed.

== Installation ==

Installing should be a piece of cake and take fewer than five minutes.

1. Upload `mrss.php` to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==

* Fixed issue with unencoded ampersands. Thanks to Justin Chen for reporting this and testing the patch.