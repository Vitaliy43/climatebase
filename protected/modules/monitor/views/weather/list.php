<script type="text/javascript">
    $( document ).ready(function() {
        $('.check-row').find('input').change(function() {
          var href = $(this).closest('.check-row').next().find('a').attr('href');
          var arr = href.split('?');
          var base_href = arr[0];
          var arr2 = arr[1].split('&');
          var arr3 = arr2[0].split('=');
          var station = arr3[1];
          var arr3 = arr2[1].split('=');
          var period_id = arr3[1];
          var checked_months = '';
          $(this).closest('.check-row').find('input').each(function( index ) {
              if ($(this).prop('checked') == true) {
                  checked_months += $(this).data('month')+',';
              }
          });
          var new_href = base_href+'?station='+station+'&period_id='+period_id+'&checked_months='+checked_months;
          $(this).closest('.check-row').next().find('a').attr('href',new_href);         
        });
    }); 
</script>

<div class="list_stations">
    <?php foreach ($list as $station=>$row) { ?>
        <div class="row">
            <div class="station"><b><?=$station?></b></div>
            <div class="data">
                <table>
                    <tr>
                        <th>period_id</th>
                        <th>year</th>
                        <th>jan</th>
                        <th>feb</th>
                        <th>mar</th>
                        <th>apr</th>
                        <th>may</th>
                        <th>jun</th>
                        <th>jul</th>
                        <th>aug</th>
                        <th>sep</th>
                        <th>oct</th>
                        <th>nov</th>
                        <th>dec</th>
                        <th>actions</th>
                    </tr>
                    <?php foreach ($row as $period_id=>$data) { ?>
                        <?php if ($period_id == 7 || $period_id == 2 || $period_id == 6 || $period_id == 3) { ?>
                            <tr data-station="<?=$station?>" data-period="<?=$period_id?>" class="check-row">
                                <td></td>
                                <td></td>
                                <td><input type="checkbox" data-month="1"/></td>
                                <td><input type="checkbox" data-month="2"/></td> 
                                <td><input type="checkbox" data-month="3"/></td>
                                <td><input type="checkbox" data-month="4"/></td>
                                <td><input type="checkbox" data-month="5"/></td>
                                <td><input type="checkbox" data-month="6"/></td>
                                <td><input type="checkbox" data-month="7"/></td>
                                <td><input type="checkbox" data-month="8"/></td>
                                <td><input type="checkbox" data-month="9"/></td>
                                <td><input type="checkbox" data-month="10"/></td>
                                <td><input type="checkbox" data-month="11"/></td>
                                <td><input type="checkbox" data-month="12"/></td>
                                <td></td>
                            </tr>
                            <tr>
                            <td align="center"><?=$period_id?></td>
                            <td align="center"><?=$data['year']?></td>
                            <td align="center"><?=$data['jan']?></td>
                            <td align="center"><?=$data['feb']?></td>
                            <td align="center"><?= $data['mar']?></td>
                            <td align="center"><?=$data['apr']?></td>
                            <td align="center"><?=$data['may']?></td>
                            <td align="center"><?=$data['jun']?></td>
                            <td align="center"><?=$data['jul']?></td>
                            <td align="center"><?=$data['aug']?></td>
                            <td align="center"><?=$data['sep']?></td>
                            <td align="center"><?=$data['oct']?></td>
                            <td align="center"><?=$data['nov']?></td>
                            <td align="center"><?=$data['dec']?></td>
                            <td align="center"><a target="_blank" href="http://climatebase.loc/monitor/weather/correctdata?station=<?=$station?>&period_id=<?=$period_id?>">Корректировать</a></td>
                        </tr>
                        <?php } ?>
                        
                    <?php } ?>
                </table>
            </div>
        </div>
    
    <?php } ?>
</div>
