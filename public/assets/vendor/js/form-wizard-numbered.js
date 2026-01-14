"use strict";
$(function () {
  var e = $(".select2"),
    t = $(".selectpicker");
  t.length && t.selectpicker(),
    e.length &&
      e.each(function () {
        var e = $(this);
        e.wrap('<div class="position-relative"></div>'),
          e.select2({
            placeholder: "Select value",
            dropdownParent: e.parent(),
          });
      });
}),
  (function () {
    var e = document.querySelector(".wizard-numbered"),
      t = [].slice.call(e.querySelectorAll(".btn-next")),
      l = [].slice.call(e.querySelectorAll(".btn-prev")),
      r = e.querySelector(".btn-submit");
    if (null !== e) {
      const c = new Stepper(e, { linear: !1 });
      t &&
        t.forEach((e) => {
          e.addEventListener("click", (e) => {
            c.next();
          });
        }),
        l &&
          l.forEach((e) => {
            e.addEventListener("click", (e) => {
              c.previous();
            });
          }),
        r &&
          r.addEventListener("click", (e) => {
            alert("Submitted..!!");
          });
    }
  })();
