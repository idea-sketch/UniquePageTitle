# Purpose
UniquePageTitle is a parser extension that adds a number suffix to a page title if a page with the same title already exists and returns this adjusted page title. The extension can be useful in combination with extensions such as [VisualData](https://www.mediawiki.org/wiki/Extension:VisualData) or [PageForms](https://www.mediawiki.org/wiki/Extension:PageForms).

# Usage
The #uniquepagetitle parser function takes the page title as parameter

<pre>{{#uniquepagetitle:My page}}</pre>

The next highest number is always used, even if pages have been deleted previously. For this purpose, the extension checks both the page AND the archive table in the database.

Usage with Extension [VisualData](https://www.mediawiki.org/wiki/Extension:VisualData)

1. Create a new field under 'properties' in your VisualData schema, i.e. 'unique pagetitle'
2. In 'value formula' insert `{{#uniquepagetitle:<my field>}}` where `<my field>` is the field the page title will be generated from
3. In the form call add the following:
<pre>
{{#visualdataform:
...
|pagename-formula = &#60;unique pagetitle&#62;
...
}}
</pre>

# Examples
<pre>{{#uniquepagetitle|My Page}} will become 'My Page 1' if 'My Page' already exists</pre>
<pre>{{#uniquepagetitle|My Page}} will become 'My Page 2' if 'My Page 1' already exists</pre>
<pre>{{#uniquepagetitle|My Page}} will become 'My Page 3' if 'My Page 1' and 'My Page 2' already exist and also if one of them has been deleted before</pre>
<pre>{{#uniquepagetitle|My Page 1}} will become 'My Page 1 1' if 'My Page 1' already exists</pre>


# Installation

* Go to the extensions directory of your MediaWiki installation.
* Clone the extension from gitlab
* Edit your `LocalSettings.php` and add at the end `wfLoadExtension( 'UniquePageTitle' );`
* Done – Navigate to `Special:Version` on your wiki to verify that the extension is successfully installed.

# Configuration

Currently there is nothing to configure.

# License

The software is usable under the GNU General Public License v2.0 or later, for details see [LICENSE](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html.en).

# Compatibility

The extension should work with MediaWiki >= 1.39.0 (tested with 1.41.0). It might also work with older versions but this has not been tested.

# History

* 2024-05-01 Version 1.1.0 Added namespace support.
* 2024-03-28 Version 1.0.0 Initial commit.
