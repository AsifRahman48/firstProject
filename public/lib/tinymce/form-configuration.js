tinymce.init({
    selector: 'textarea',
    height: 300,
    theme: 'modern',
    plugins: 'spellchecker print preview fullpage searchreplace autolink directionality  visualblocks visualchars fullscreen link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount contextmenu colorpicker imagetools textpattern help advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table contextmenu directionality emoticons template paste textcolor colorpicker textpattern imagetools',
    toolbar: 'spellchecker formatselect fontselect fontsizeselect | bold italic strikethrough forecolor backcolor emoticons | link image print preview media | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat insertfile undo redo | styleselect',
    fontsize_formats: '8pt 10pt 12pt 14pt 16pt 18pt 20pt 24pt 36pt',
    browser_spellcheck: true,
    image_advtab: true,
    contextmenu: false,
    spellchecker_languages: 'English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr_FR,' + 'German=de,Italian=it,Polish=pl,Portuguese=pt_BR,Spanish=es,Swedish=sv',
    content_css:['http://ams.psgbd.com/lib/tinymce/skins/mycustom.css'],

    image_title: true,
    automatic_uploads: true,
    images_upload_url: BASE_URL+'/file_upload_tinymce',
    relative_urls : false,
    remove_script_host : false,
    convert_urls : true,
    file_picker_types: 'image',
    images_upload_handler: function (blobInfo, success, failure) {
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', BASE_URL+'/file_upload_tinymce');
        var token = document.getElementById("_token").value;
        xhr.setRequestHeader("X-CSRF-Token", token);
        xhr.onload = function() {
            var json;
            if (xhr.status != 200) {
                failure('HTTP Error: ' + xhr.status);
                return;
            }
            json = JSON.parse(xhr.responseText);

            if (!json || typeof json.location != 'string') {
                failure('Invalid JSON: ' + xhr.responseText);
                return;
            }
            success(json.location);
        };
        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
    },
    file_picker_callback: function(cb, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.onchange = function() {
            var file = this.files[0];

            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
                var id = 'blobid' + (new Date()).getTime();
                var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                var base64 = reader.result.split(',')[1];
                var blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);
                cb(blobInfo.blobUri(), { title: file.name });
            };
        };
        input.click();
    },

    allow_conditional_comments: false,
    setup: function (ed) {
        ed.on('init', function (e) {
            ed.execCommand("fontName", false, "Conv_TelenorLight");
        });
    },
    font_formats: 'Telenor=Conv_TelenorLight; Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings',
    spellchecker_callback: function(method, text, success, failure) {
        var words = text.match(this.getWordCharPattern());
        if (method == "spellcheck") {
            var suggestions = {};
            for (var i = 0; i < words.length; i++) {
                suggestions[words[i]] = ["First", "Second"];
            }
            success(suggestions);
        }
    }
});