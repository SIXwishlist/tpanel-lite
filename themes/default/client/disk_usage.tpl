<script type="text/javascript" src="{{ @theme('js/Chart.min.js') }}"></script>
<script type="text/javascript">
window.onload = function () {
	var stats = {{usageData}};
	var ctx = document.getElementById("chart").getContext("2d");
	var statsPie = new Chart(ctx).Pie(stats);
};
</script>
<canvas style="height:300px" id="chart">

</canvas>
<div class="stats">
	Usage: {{@usage}}
	Available: {{@available}}
</div>
