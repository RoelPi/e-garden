// Shout out to https://stackoverflow.com/questions/14766951/convert-digits-into-words-with-javascript
var a = ['','one ','two ','three ','four ', 'five ','six ','seven ','eight ','nine ','ten ','eleven ','twelve ','thirteen ','fourteen ','fifteen ','sixteen ','seventeen ','eighteen ','nineteen '];
var b = ['', '', 'twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety'];

function toWords (num) {
	if (num == 0) { return 'zero';}
    if ((num = num.toString()).length > 9) return 'overflow';
    n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
    if (!n) return; var str = '';
    str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
    str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
    str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
    str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
    str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) : '';
    return str;
}

// Global variables :(
var counter = 0;
var w;
var status = "nowater";
var noInterrupt = 0;

// Shout out to https://stackoverflow.com/questions/979975/how-to-get-the-value-from-the-get-parameters
function gup(name, url) {
    if (!url) url = location.href;
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( url );
    return results == null ? null : results[1];
}
token = gup('token');
test = gup('test');

// Function that requests water and appropriately starts timing
function water(seconds) {
	$("#btn").css({'border': 'solid #8f8f8f 2px','background': '#bfbfbf'});
	$("#btn").prop("disabled",true);
	noInterrupt = 1;
	$.getJSON("request.php?token=" + token + "&sec=" + seconds + "&test=" + test , function(data) {
		status = data.status;
		delay = data.delay - 2; // -2 for callibration
		if (delay < 0) { delay = 0; }
		s = data.sec;
		if (status == "water") {
			countdown = setInterval(function() {
				if (delay == 0) {
					countwater = setInterval(function() {
						if (s == 0) {
							$("#help").html("Tapping this button will water the plants for <span class='highlight'>5 seconds</span>");
							noInterrupt = 0;
							$("#btn").prop("disabled",false);
							$("#btn").css({'border': 'solid #2f6627 2px','background': '#2f6627'});
							$("#btn").hover(function() {
								$(this).css({'background': '#39852d'});
							},function() {
								$(this).css({'background': '#2f6627'});
							});
							clearInterval(countwater);
							getLatest();
						} else {
							$("#help").html("Giving water. <span class='warning'>" + s + " seconds</span> remaining.");
						}
						s -= 1;
					},1000);
					clearInterval(countdown);
				} else {
					$("#help").html("In <span class='warning'>" + delay + " seconds</span>, the pump will water your plants for <span class='highlight'>" + s + " seconds</span>.");
				}
			delay -= 1;
			},1000);
		} else if (status = "notoken") {
			setTimeout(function() {
				$("#help").html("<span class='warning'>Invalid token</span>. No water for your plants, my friend.");
			},3000);
		}
	});
	counter = 0;
}

// Run this function once the button has been pushed. Waits for three seconds to request water
function startTiming(seconds) {
	if (noInterrupt == 0) {
		counter += seconds;
		more = counter + 5;
		$('#help').html("Tapping this button <u>again</u> will water the plants for <span class='highlight'>"+ more + " seconds</span>.<br>");
		clearTimeout(w);
		w = setTimeout(water,3000,counter);
	}
}

function getLatest() {
	$.getJSON("latest.php?token=" + token, function(data) {
		$("#date").text(data.date);
		$("#hour").text(data.hour);
		$("#daycount").text(toWords(parseInt(data.diff)) + " days");
		$("#sec").text(toWords(parseInt(data.sec)) + " seconds");
	});
}

$(document).ready(function() {
	getLatest();
	$('#btn').click(function() {
		startTiming(5);
	});
});

