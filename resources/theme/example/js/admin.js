$(document).ready(function() {
  if (window.Quill  && $('#editor-container').length) {
    Quill.prototype.getHtml = function() {
      return this.container.querySelector('.ql-editor').innerHTML;
    };

    var quill = new Quill('#editor-container', {
      modules: {
        toolbar: {
          container: '#toolbar-container',
          handlers: {
            image: imageHandler
          }
        }
      },
      placeholder: 'Compose an epic new post...',
      theme: 'snow'
    });

    function imageHandler() {
      var range = this.quill.getSelection();

      bootbox.prompt('What is the image URL', function(result) {
        quill.insertEmbed(range.index, 'image', result, Quill.sources.USER);
      });
    }

    var txtArea = document.createElement('textarea');
    txtArea.name = 'editorData';
    txtArea.style.cssText = "width: 100%;margin: 0px;background: rgb(29, 29, 29);box-sizing: border-box;color: rgb(204, 204, 204);font-size: 15px;outline: none;padding: 20px;line-height: 24px;font-family: Consolas, Menlo, Monaco, &quot;Courier New&quot;, monospace;position: absolute;top: 0;bottom: 0;border: none;display:none"
    txtArea.value = quill.getHtml();

    var htmlEditor = quill.addContainer('ql-custom')
    htmlEditor.appendChild(txtArea)

    var myEditor = document.querySelector('#editor-container')
    quill.on('text-change', (delta, oldDelta, source) => {
      txtArea.value = quill.getHtml();
    })

    var customButton = document.querySelector('.ql-showHtml');
    customButton.addEventListener('click', function() {
      if (txtArea.style.display === '') {
        var html = txtArea.value
        quill.pasteHTML(html)
      }
      txtArea.style.display = txtArea.style.display === 'none'
        ? ''
        : 'none'
    });
  }

  $("form#content-creation").on("submit", function(e) {
    e.preventDefault();

    $.post($(this).attr("action"), $(this).serialize(), function(data) {
      var options =  {
          content: '<center>' + (data.error ? data.error : data.result) + '</center>', // text of the snackbar
          style: "toast", // add a custom class to your snackbar
          timeout: 2000, // time in milliseconds after the snackbar autohides, 0 is disabled
          htmlAllowed: true
      }

      $.snackbar(options);
    }, 'json');
  });
});
