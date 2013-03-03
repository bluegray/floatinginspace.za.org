# GoogleBot RSS feed script
## The Python script
This python script will go through your apache access log and make an rss feed of the latest crawls by GoogleBot, or any other string you want to keep an eye on.

Change the maximum number of entries displayed to suit your needs.
The time_adjust variable will add the amount in hours to the displayed log time. This is useful if the server is not in your timezone.

It might be a good idea to password protect this feed, you probably don't want everyone to see it.
