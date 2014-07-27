<script type="text/javascript" src="{{ @theme('js/jquery.js') }}"></script>
<script type="text/javascript">
jQuery(document).ready(function () {
	jQuery.get('{{ @url('api/files') }}', function (data) {
		console.log(data);
	});
});
</script>
