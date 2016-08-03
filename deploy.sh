#clear
PLUGIN=bigbluebuttonbn

#MOODLES[0]="moodle"
#MOODLES[1]="moodle26"
#MOODLES[2]="moodle27"
#MOODLES[3]="moodle28"
#MOODLES[4]="moodle29"
#MOODLES[5]="moodle30"
MOODLES[6]="moodle31"

for i in "${MOODLES[@]}"
  do
    echo $i
    BASE=/var/www/html/$i/lib/editor/tinymce/plugins/$PLUGIN
    sudo rm -rf $BASE/
    sudo cp -r . $BASE/
    sudo rm -rf $BASE/.git
    sudo chown -R www-data.www-data $BASE/
done
