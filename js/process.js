function setAnswer(value, no) {
    var answer = value;
    $("#AnswerValue").val(answer);
    $("[id^='a']").css("background-color", "lightslategray");
    $("#a" + no +"").css("background-color", "#616161");
    $("#notify").hide();
    $("#next").show();
}
$(document).ready(function(){
    $("#next").click(function(){
        //Get answer id and set in variable
        var answer = $("#AnswerValue").val();
        var dataString = 'answer='+ answer;

        if(answer == '') {
            alert("Lūdzu noklikšķiniet uz atbilžu varianta!");
        } else {
            // AJAX Code To Submit questions form.
            $.ajax({
                type: "POST",
                url: "app/ajaxform.php",
                data: dataString,
                cache: false,
                success: function(result){

                    if (result == 'done') {
                        window.open("result.php", "_self");
                    } else {
                        $( "#questions-block" ).empty();
                        $("#questions-block").html(result);
                        var title = $("#question-title").val();
                        $(".title").text(title);
                        var progress = $("#progress").val();
                        $("#current-progress").css("width", progress + "%");
                        $("#AnswerValue").val(0);
                        $("#next").hide();
                        $("#notify").show();
                    }
                }
            });
        }

        return false;
    });
});