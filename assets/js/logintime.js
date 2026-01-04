function startTime() {
  var today = new Date();
  var h = today.getHours();
  var m = today.getMinutes();
  var s = today.getSeconds();
  var am_pm = today.getHours() >= 12 ? "PM" : "AM";
  h = h % 12;
  h = h ? h : 12;
  m = checkTime(m);
  s = checkTime(s);
  document.getElementById("sec").innerHTML = ":" + s + " " + am_pm;
  document.getElementById("hr-mn").innerHTML = h + ":" + m;
  var t = setTimeout(startTime, 500);
  var d = new Date();
  var months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  document.getElementById("dtnow").innerHTML =
    months[d.getMonth()] + " " + d.getDate() + ", " + d.getFullYear();
}
function checkTime(i) {
  if (i < 10) {
    i = "0" + i;
  } // add zero in front of numbers < 10
  return i;
}

//ajax for login
$(document).ready(function () {
  $(".lg-button").click(function () {
    var uname = $("#uname").val();
    var pass = $("#pass").val();
  });
});
function fnct() {
  window.location.href = "index.php";
}
$(document).ready(function () {
  // var ips="sd";

  //   $.getJSON('https://json.geoiplookup.io/?callback=?', function(data) {
  //     ips=data.ip;

  //   });

  //question answer js
  $("#YS_ANS").click(function () {
    $(this).prop("disabled", true);
    $("#modalSuccess").modal("toggle");
    var data = $(this).val();
    var ip = "";
    $.ajax({
      url: "query/Query-queslogin.php",
      data: data,
      type: "POST",

      success: function (data) {
        $(this).prop("disabled", false);
        location.replace("http://dashboard.wedoinc.ph/index.php");
      },
    });
  });

  $("#uname").focus(function () {
    //tmdate-display
    // $(".tmdate-display").hide();
  });
  $("#pass").focus(function () {
    //tmdate-display
    // $(".tmdate-display").hide();
  });

  // $("#pass").keypress(function (event) {
  //   var keycode = event.keyCode ? event.keyCode : event.which;
  //   if (keycode == "13") {
  //     var uname = $("#uname").val();
  //     var pass = $("#pass").val();
  //     if (uname == "" || pass == "") {
  //       $(".lg-warning").text("Unknown Username or Password !");
  //       $(".lg-warning").css("display", "block");
  //       return false;
  //     } else {
  //       var data = $(".loginform").serialize();
  //       $.ajax({
  //         url: "query/query-login.php",
  //         data: data,
  //         type: "POST",

  //         success: function (data) {
  //           // if (data=="false"){
  //           //   alert("IC");
  //           // }

  //           if (data == 0) {
  //             $(".lg-warning").text("Incorrect Username !");
  //             $(".lg-warning").css("display", "block");
  //           } else if (data == 7) {
  //             location.replace("http://dashboard.wedoinc.ph/questionnaire.php");
  //           } else if (data == 1) {
  //             $(".lg-warning").text("Incorrect Password !");
  //             $(".lg-warning").css("display", "block");
  //           } else {
  //             location.reload(true);
  //           }
  //         },
  //       });
  //     }
  //   }
  // });

  // $("#uname").keypress(function (event) {
  //   var keycode = event.keyCode ? event.keyCode : event.which;
  //   if (keycode == "13") {
  //     var uname = $("#uname").val();
  //     var pass = $("#pass").val();
  //     if (uname == "" || pass == "") {
  //       $(".lg-warning").text("Unknown Username or Password !");
  //       $(".lg-warning").css("display", "block");
  //       return false;
  //     } else {
  //       var data = $(".loginform").serialize();
  //       $.ajax({
  //         url: "query/query-login.php",
  //         data: data,
  //         type: "POST",

  //         success: function (data) {
  //           if (data == 0) {
  //             $(".lg-warning").text("Incorrect Username !");
  //             $(".lg-warning").css("display", "block");
  //           } else if (data == 7) {
  //             location.replace("http://localhost/weodo/questionnaire.php");
  //           } else if (data == 1) {
  //             $(".lg-warning").text("Incorrect Password !");
  //             $(".lg-warning").css("display", "block");
  //           } else {
  //             location.reload(true);
  //           }
  //         },
  //       });
  //     }
  //   }
  // });

  $(".btn-success").click(function () {});
});
