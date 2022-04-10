Dropzone.autoDiscover = false;
const formNode = document.querySelector('#add-task-form');

if (formNode) {
  let myDropzone = new Dropzone("div#dropzone", {
    url: "/tasks/upload-files",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    paramName: 'Task[files]',
    uploadMultiple: true,
    maxFiles: 10,
    parallelUploads: 20,
    autoProcessQueue: false,
    addRemoveLinks: true,
  });

  $('#add-task-form').on('beforeSubmit', function (evt) {
    if (myDropzone.getQueuedFiles().length) {
      myDropzone.processQueue();
      return false;
    }
  });

  myDropzone.on("successmultiple", () => {
    $('#add-task-form').yiiActiveForm('submitForm');
  });
}
