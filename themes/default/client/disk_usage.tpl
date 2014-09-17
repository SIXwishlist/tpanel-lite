<script type="text/javascript" src="{{ @theme('js/Chart.min.js') }}"></script>
<script type="text/javascript">
window.onload = function () {
	var stats = {{usageData}};
	var ctx = document.getElementById("space-usage").getContext("2d");
	var statsPie = new Chart(ctx).Pie(stats, {
	'tooltipTemplate':"<%=label%>",
	animateRotate : false,
	animationEasing : "easeOut",
	animationSteps: 60,
	animateScale: true
	});
};
</script>
<p>The space usage overview below details some of the largest files and directories that account for the bulk of your web space usage.</p>
<canvas height="400" width="400" id="space-usage">

</canvas>
<div class="stats">
	Usage: {{@usage}}
	Available: {{@available}}
</div>
