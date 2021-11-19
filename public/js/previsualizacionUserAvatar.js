window.onload = function() {

    document.getElementById("user_update_form").onchange = function(e) {

        if (!e.target.files[0].name.match(/\.(jpe?g|png|gif)$/i)) {
            alert('La imagen debe ser un fichero JPG, PNG o GIF');
            e.target.value = "";

        } else {
            let reader = new FileReader();
            reader.readAsDataURL(e.target.files[0]);

            reader.onload = function() {
                let image = document.getElementById('preview');
                image.src = reader.result;
            }
        }
    }
}