<footer class="main-footer">
    <strong><?= constant('CMSNOME').'-'.constant('CMSDESCRICAO')?> &copy; <?= constant('CMSANODESENVOLVIMENTO')?>-<?= date('Y')?>.</strong>
    Todos os direitos reservados.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> <?= constant('CMSVERSAO')?>
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->

<script src="<?= base_url('assets/admin/plugins/jquery/jquery.min.js');?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= base_url('assets/admin/plugins/jquery-ui/jquery-ui.min.js');?>"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js');?>"></script>
<!-- ChartJS -->
<script src="<?= base_url('assets/admin/plugins/chart.js/Chart.min.js');?>"></script>
<!-- Sparkline -->
<script src="<?= base_url('assets/admin/plugins/sparklines/sparkline.js');?>"></script>
<!-- JQVMap -->
<script src="<?= base_url('assets/admin/plugins/jqvmap/jquery.vmap.min.js');?>"></script>
<script src="<?= base_url('assets/admin/plugins/jqvmap/maps/jquery.vmap.usa.js');?>"></script>
<!-- jQuery Knob Chart -->
<script src="<?= base_url('assets/admin/plugins/jquery-knob/jquery.knob.min.js');?>"></script>
<!-- daterangepicker -->
<script src="<?= base_url('assets/admin/plugins/moment/moment.min.js');?>"></script>
<script src="<?= base_url('assets/admin/plugins/daterangepicker/daterangepicker.js');?>"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url('assets/admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js');?>"></script>
<!-- Summernote -->
<script src="<?= base_url('assets/admin/plugins/summernote/summernote-bs4.min.js');?>"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url('assets/admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js');?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('assets/admin/dist/js/adminlte.js');?>"></script>
<!-- Bootstrap Switch -->
<script src="<?= base_url('assets/admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js')?>"></script>
<!-- DataTables -->
<script src="<?= base_url('assets/admin/plugins/datatables-bs5/datatables.min.js')?>"></script>

<script src="https://unpkg.com/cropperjs"></script>

<script src="<?= base_url('assets/admin/plugins/jquerymask/jquery.mask.min.js')?>"></script>


<script src="<?= base_url('assets/admin/dist/js/pages/footer.js');?>"></script>


</body>
</html>

<script>
  $(document).ready(function() {
    $('.money').mask('#.##0,00', { reverse: true });
  });
</script>

<script>


  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

</script>

<script>
$('#summernote').summernote({
  height: 500,                 
  minHeight: null,             
  maxHeight: null,             
});

//Date picker
$('#reservationdate').datetimepicker({
    format: 'L'
});

//bootstrap-switch
$("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    })
</script>

<script>

$(document).ready(function(){


  $("#submit").click(function(){
    
    $(this).text('Carregando...');
    

    });


  var $modal = $('#modal');
  var image = document.getElementById('sample_image');
  var cropper;

  //crop
  $('#upload_image').change(function(event){
      var files = event.target.files;
      var done = function (url) {
          image.src = url;
          $modal.modal('show');
      };

      if (files && files.length > 0)
      {
        reader = new FileReader();
        reader.onload = function (event) {
            done(reader.result);
        };
        reader.readAsDataURL(files[0]);
          
      }
  });

  $modal.on('shown.bs.modal', function() {
      cropper = new Cropper(image, {
        aspectRatio: 4/3,
        viewMode: 0,
        preview: '.preview'
      });
  }).on('hidden.bs.modal', function() {
      cropper.destroy();
      cropper = null;
  });

  $("#crop").click(function(){

      canvas = cropper.getCroppedCanvas({
          width: 600,
          height: 400,
      });

      canvas.toBlob(function(blob) {
          var reader = new FileReader();
          reader.readAsDataURL(blob); 
          reader.onloadend = function() {
              var base64data = reader.result;  
            
              $.ajax({
                url: "/UploadImage/upload",
                  method: "POST",                	
                  data: {image: base64data},
                  success: function(data){
                      console.log(data);
                      $modal.modal('hide');
                      $('#capa').val(data);
                      $('#uploaded_image').attr('src', data);
                  }
                });
          }
      });
    });

    //crop


});







</script>

