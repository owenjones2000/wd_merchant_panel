<script>
    layui.use([],function () {
        $.get("{{ route('advertise.dashboard.data') }}",
            {},
            function (result) {
                if (result.code==0){
                    $('#impressions').text(result['data']['20200228']['impressions']);
                    $('#clicks').text(result['data']['20200228']['clicks']);
                    $('#installs').text(result['data']['20200228']['installs']);
                    $('#spends').text(result['data']['20200228']['spend']);
                    $('#ir').text(result['data']['20200228']['ir'] + '%');
                    $('#ctr').text(result['data']['20200228']['ctr'] + '%');
                    $('#cvr').text(result['data']['20200228']['cvr'] + '%');
                    $('#ecpm').text(result['data']['20200228']['ecpm']);

                }
            });
    });
</script>
