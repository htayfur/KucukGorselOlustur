<?php

/*
Plugin Name: Küçük Görsel Oluştur
Plugin URI:  http://htayfur.com
Description: Yazı içerisindeki ilk fotoğrafı galeriye ekler ve öne çıkarılmış görsel olarak tanımlar.
Version:     1.0
Author:      Hakan Tayfur
Author URI:  http://htayfur.com/benkimim/
License:     Private
License URI: http://htayfur.com/lisans
*/

function menuyeekle(){
    add_submenu_page('options-general.php','Küçük Görsel Oluştur',10,__FILE__,'EklentiFonksiyon');
}

add_action('admin_menu','menuyeekle');

function EklentiFonksiyon(){

    if($_POST['onay'] != "Evet"){
        $yazilar = get_posts(array('numberposts'=>'-1','post_status'=>array('draft','publish','pending')));

        foreach($yazilar as $yazi){
            $yazi_id = $yazi->ID;
            $yazi_baslik = $yazi->post_title;
            $yazi_icerik = $yazi->post_content;
            if(!has_post_thumbnail($yazi_id)){
                preg_match('@<img(.*?)src="(.*?)"(.*?)>@',$yazi_icerik,$ilkresim);
                $fotograf_adi = str_replace(get_home_url(),'',$ilkresim[2]);
                $dosya_tipi = wp_check_filetype(basename($fotograf_adi),null);
                $wp_upload_yolu = "/wp-content/uploads/imgs/";

                $ek = array(
                    "guid" => $wp_upload_yolu,
                    "post_mime_type" => $dosya_tipi['type'],
                    "post_title" => preg_replace('/\.[^.]+$/','',basename($fotograf_adi)),
                    "post_content" => '',
                    "post_status" => 'inherit'
                );

                $ek_id = wp_insert_attachment($ek,$fotograf_adi,$yazi_id);

                require_once(ABSPATH .'wp-admin/includes/image.php');
                $ek_veri = wp_generate_attachment_metadata($ek_id,$fotograf_adi);
                wp_update_attachment_metadata($ek_id,$ek_veri);

                set_post_thumbnail($yazi_id,$ek_id);

                echo $yazi_baslik." isimli başlığa yeni bir öne çıkarılmış görsel eklendi!<br>";
            }else{
                echo $yazi_baslik." zaten bir öne çıkarılmış görsele sahip!<br>";
            }
        }
    }else{
        echo '<div style="width: 600px;margin: 0 auto;margin-top: 30px;">
<form action="" method="POST">
    <p>Eklenmiş bütün yazılara öne çıkarılmış görsel tanımlamak istiyor musunuz?</p><br>
    <p>(Daha önce tanımlanmış öne çıkarılmış görseller tekrar tanımlanmayacaktır.)</p><br>
    <input type="hidden" name="onay" value="Evet">
    <input class="button button-primary button-hero install-now" type="submit" value="Öne Çıkarılmış Görselleri Oluştur!">
</form>
</div>';
    }

}

function EklentiKurulum(){

}

function EklentiKaldir(){

}

function EklentiPasif(){

}

?>