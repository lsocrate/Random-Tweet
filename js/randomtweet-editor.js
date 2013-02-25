(function() {

  jQuery(function($) {
    var counter;
    counter = $("#charcount .charactercount-count");
    return $("#title").on("keyup", function(ev) {
      var count;
      count = $(this).val().length;
      return counter.text(count);
    });
  });

}).call(this);
