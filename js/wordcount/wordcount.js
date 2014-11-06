
function limitWords(editor,max_count){
    $("#display_count").html( "0 Words of " + max_count+ " remaining");
    var wordCounts = {};
    $(editor).keyup(function () {
        
        var matches = this.value.match(/\b/g);
        wordCounts[this.id] = matches ? matches.length / 2 : 0;
        var finalCount = 0;
        $.each(wordCounts, function (k, v) {
            finalCount += v;
        });
        var vl = this.value;
        if (finalCount > max_count) {
            vl = vl.substring(0, vl.length - 1);
            this.value = vl;
        }
        $("#display_count").html(finalCount + " Words of " + (max_count - finalCount) + " remaining");
    }).keyup;
}

function limitChars(editor, max_count){
    $("#display_count").html( "0 Characters of " + max_count+ " remaining");
$(editor).keyup(function(){
    var text = $(editor).val(); 
    var length = text.length;
    if(length > max_count){
       $(editor).val(text.substr(0,max_count));
     } else { 
        $("#display_count").html(length + " Characters of " + (max_count - length) + " remaining");
        //$('#count_left').html(max_count -length);
     }

});


 }