jQuery(($) ->
  counter = $("#charcount .charactercount-count")
  $("#title").on("keyup", (ev) ->
    count = $(@).val().length
    counter.text(count)
  )
)
