
const BODY = document.body;
const TAB_KEY = 9;
const TABBING_CLASS = "tabbing"

/*
* Remove the no-js class from the body if JS is turned on.  
*/
BODY.classList.remove("no-js");


/*
* Add/remove tabbing class if user is navigating via keyboard.  
*/
BODY.addEventListener("keydown", function(e) {
  if (e.which == TAB_KEY) {
    BODY.classList.add(TABBING_CLASS);
  }
});

BODY.addEventListener("mousedown", function() {
  BODY.classList.remove(TABBING_CLASS);
});