<?php

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
$( document ).ready(function() {
    $("form#emp").submit(function(){
        
        var formData = new FormData($(this)[0]);
        // alert(formData); return false;
        $.ajax({
            url: "/test/registration_inc.php?action=ajax_call&section=save_data",
            type: 'POST',
            data: formData,
            success: function (data) {
                alert(data); return false;
            },
            contentType: false,
            processData: false
        });

        return false;
    });
});

</script>

<form action="" id="emp" name="emp" method="POST" enctype='multipart/form-data' >
    <input type="hidden" name="id" value="3" />
    <p>
        <label><b>Email</b></label>
        <input type="text" placeholder="Enter Name" name="name" >
    </p>

    <p>
        <label><b>Email</b></label>
        <input type="text" placeholder="Enter Email" name="email" >
    </p>

    <p>
        <input name="image" type="file" />
    </p>
    
    <button type="submit" class="registerbtn">Register</button>
</form>