<script>
    layui.use([],function () {
        $.get("{{ route('advertise.dashboard.data', ['range_date' => 'now']) }}",
            {},
            function (result) {
                if (result.code==0){
                    var kpi = result['data'];
                    $('#impressions').text(kpi['impressions']);
                    $('#clicks').text(kpi['clicks']);
                    $('#installs').text(kpi['installs']);
                    $('#spends').text(kpi['spend']);
                    $('#ir').text(kpi['ir'] ? (kpi['ir'] + '%') : '-');
                    $('#ctr').text(kpi['ctr'] ? (kpi['ctr'] + '%') : '-');
                    $('#cvr').text(kpi['cvr'] ? (kpi['cvr'] + '%') : '-');
                    $('#ecpm').text(kpi['ecpm'] ? kpi['ecpm'] : '-');

                }
            });
    });
</script>
