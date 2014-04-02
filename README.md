Odd or Not
==========

A basic Twitter analytics website using PHP and JavaScript to generate a meaningless statistic about any public Twitter user's timeline.  It could easily be adapted to provide alternative analytics and statistics, if that's what you want to do.  The design attempts to be responsive and work on mobile devices just as well as on desktop devices.  

## Twitter API

This service requires use of the Twitter REST API, and that anyone taking the source code is able to set up a Twitter application and generate the necessary API keys via [Twitter Apps](https://apps.twitter.com). 

When you have your API keys, copy and paste them into the appropriate files found in /twitter.  Do not change the file names or add a file extension, only edit the files using a text editor to add your API keys.  

The Twitter API calls are handled by TwitterAPIExchange.php by J7mbo, found at [twitter-api-php](https://github.com/J7mbo/twitter-api-php).  Do not change anything in this file.  

## Google Analytics

If you use Google Analytics, and want to add this to your site, copy and paste your Google Analytics script into analyticstracking.php.  

If you don't need this, remove analyticstracking.php and edit index.php to remove the php_include calling it.  

## HighCharts API

Odd or Not makes use of [HighCharts JavaScript Charting Library](http://www.highcharts.com/products/highcharts) to generate the dynamic pie chart displayed on the page.  

This is an extremely easy to use API that lets you quickly add almost any style of chart to your site.  Check out the [HighCharts Demo page](http://www.highcharts.com/demo/) for examples of every type they offer, with source code to get you started quickly.  

If JavaScript is disabled, I have attempted to design the site to fall back and display results using PHP, but without the chart.  This may or may not work; your results may vary.  

## Odd or Not Analytics

Odd or Not was born out of an idea to analyse my own Tweeting behaviour.  I found, through what is more than likely OCD, I preferred to send a Tweet only on even minutes, e.g. 10:12 instead of 10:11.  I would often wait intentionally for the next even minute to send a Tweet, rather than sending it on a odd minute.

I finally decided I wanted to see just what effect this behaviour had, first for my Twitter account, and then to compare this to any Twitter account.  

A Twitter account name is typed in, including the '@'.  There is some validation in JavaScript, and PHP, to ensure the user name is a valid format, and includes the '@'.  This does not check if the user name exists on Twitter, only that it is a valid format.  

The PHP does check if the valid format user name exists on Twitter, and returns an error message if it does not.  It also returns a message if the Twitter account is Private, as Odd or Not is not able to access those Tweets.  

Assuming the user name is valid, exists on Twitter and is a public account, oddornot.php grabs the previous 1000 Tweets (or total number of Tweets if a user has less than 1000) for that Twitter account.  

For each Tweet, it looks at the time that Tweet was sent, extracts the minute and determines if this is odd or even, increasing the appropriate counter.  It then takes the Odd and Even counts, and if JavaScript is enabled generates a Pie chart, displaying the percentage of your Tweets that were made on odd minutes (e.g. 10:11 instead of 10:12).  

Since first testing it, I have found my last 1000 Tweets have gone from 18% Odd to now only 12% Odd, meaning 88% of my previous 1000 Tweets were made on even minutes.  Geeky?  Yes.  Definitely.  But I like it.  

## Design/CSS

I am not creative, or very good at making things look nice (only functional), so the design and CSS here isn't great.  I've got most of it working how I want, but there are still some issues.  

I have done my best to make it responsive, to work on mobile devices as well as desktops.  It does, and the layout resizes nicely, but there is one main issue.  

That issue is with the HighCharts object, mainly on mobile devices or in small browser windows.  The object will display the correct data, and will draw correctly using a narrower width but with large empty areas above and below the chart.  This makes the chart object considerably taller than it needs to be.  Obviously, if the width is correct, the height should be the same and these empty spaces should not be there.

If the window is scrolled or resized, sometimes the chart object then redraws the correct size, slightly larger or smaller than initially, but with the empty space removed (using the Reflow property).  I have experimented but not found the cause to this issue, or the solution.  

## Further Development

I have no big plans to continue work on this, it was only ever a silly idea I had to be able to analyse my own Tweets.  I've achieved what I set out to do, and the website is out there at [OddOrNot.me](http://oddornot.met) for anyone to play around with.

If I do continue, these are a few things I want to look at:
- Twitter Rate Limit
- Caching (for above issue, and to improve performance)
- Time lag - reduce the time it takes to process 1000 tweets
- HighCharts object size issues on smaller browswer windows
- CSS improvements

Feel free to look at this and let me know any suggested improvements you would make.  