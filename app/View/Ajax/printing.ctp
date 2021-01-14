<script>
    $(document).ready(function () {
        var datatoprint = '<?php echo $datatoprint;?>';
        if (datatoprint == "leaderboard") {
            var htmldata = window.opener.document.getElementById("leaderboardprinting").innerHTML;
            document.body.innerHTML = htmldata;
            setTimeout(function () {
                $(".table-striped a").removeAttr("href");
                $("#searchleaderboard").addClass("hidden");
                $(".dropdown").addClass("hidden");
                window.print();
                window.close();
            }, 1000)

        }else if (datatoprint == "leaderboardzoom") {
            var htmldata = window.opener.document.getElementById("leaderboardprintingzoom").innerHTML;
            document.body.innerHTML = htmldata;
            setTimeout(function () {
                $("body a").removeAttr("href");
                $("#searchleaderboard").addClass("hidden");
                $(".dropdown").addClass("hidden");
                window.print();
                window.close();
            }, 1000)

        } else if (datatoprint == "listingreportser") {
            //====er is endorsement received
            var htmldata = window.opener.$("#usersdetail").html();
            var endorsementerdata = window.opener.$("#listingreportser").html();
            $('body').append(htmldata, endorsementerdata);
            $("body a").removeAttr("href");
            setTimeout(function () {
                window.print();
                window.close();
            }, 1000)

        } else if (datatoprint == "listingreportseg") {
            //====eg is endorsement given
            var userdata = window.opener.$("#usersdetail").html();
            var endorsementegdata = window.opener.$("#listingreportseg").html();
//            /htmldata = $(window.opener.document.getElementById("listingreportser")).appendTo(htmldata);
            $('body').append(userdata, endorsementegdata);
            $("body a").removeAttr("href");
            setTimeout(function () {
                window.print();
                window.close();
            }, 1000)
        } else if (datatoprint == "allendorsements") {
            var allendorsementdata = window.opener.$("#allendorsementsdata").html();
            var orgdata = window.opener.$(".comp-name").html();
            $('body').append(orgdata, allendorsementdata);
            $("body a").removeAttr("href");
            setTimeout(function () {
                window.print();
                window.close();
            }, 1000)
        } else if (datatoprint == "complete") {
            var userdata = window.opener.$("#usersdetail").html();
            var endorsementerdata = window.opener.$("#listingreportser").html();
            var endorsementegdata = window.opener.$("#listingreportseg").html();
            
            $('body').append(userdata, "<br/><br/>", endorsementerdata, "<br/><br/>",endorsementegdata);
            $("body a").removeAttr("href");
            setTimeout(function () {
                window.print();
                window.close();
            }, 1000)
        }
		 $(".navbar-collapse").remove()
    });
</script>