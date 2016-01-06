# Ann Droid Artist

_Note: This is a work in progress and is likely to break without notice_

This project drives a drawbot based on a Raspberry Pi and two stepper motors. Effectively it's a 
plotter without the z-axis (to lift the pen off the page).

Check out some results at http://blog.joel.ly/say-hello-world-to-drawbot/

Further details will eventually be provided in a wiki but there are still a number of milestones 
to hit before this happens:

1. Imaging pipeline: I'm still not decided on whether to use GeoJson or SVG for input. I'd 
   prefer SVG but supporting it is more complex but there are pros and cons for either.
2. Code refactoring: at least when #1 above has been decided I can prune off irrelevant code. Plus,
   uh, it's probably not up to the standards as I'd like (PSR, etc). As there is exactly 1 
   implementation of this code it'll remain as-is for now.

