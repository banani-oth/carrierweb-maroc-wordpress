<?php
/** Editorial controls for copy, media and links. @package CarrierWeb */
if (!defined('ABSPATH')) {exit;}

function cw_editor_sections(): array {
    return array(
        'site'=>'Coordonnées et présentation de l’entreprise', 'hero'=>'Accueil — présentation', 'trustBar'=>'Accueil — engagements',
        'videoShowcase'=>'Accueil — vidéo', 'services'=>'Accueil — solutions', 'why'=>'Accueil — avantages',
        'sectorsHome'=>'Accueil — secteurs', 'dashboard'=>'Accueil — aperçu de la plateforme', 'partners'=>'Accueil — compatibilité',
        'testimonials'=>'Accueil — témoignages', 'homeFaq'=>'Accueil — questions fréquentes', 'cta'=>'Accueil — appel à contact',
        'header'=>'En-tête et navigation', 'footer'=>'Pied de page', 'listing'=>'Listes des solutions et secteurs',
        'solutionDetail'=>'Fiches solutions — libellés', 'sectorDetail'=>'Fiches secteurs — libellés', 'whyPage'=>'Page Pourquoi CarrierWeb',
        'referencesPage'=>'Page Références', 'resources'=>'Page Ressources', 'articlePage'=>'Articles — libellés',
        'contact'=>'Page Contact', 'clientArea'=>'Page Espace Client', 'auth'=>'Connexion et demande d’accès',
        'common'=>'Libellés communs et accessibilité', 'meta'=>'Titres et descriptions pour les moteurs de recherche',
    );
}
function cw_editor_locale($value): string {
    return is_string($value)&&in_array($value,array('fr','en','ar'),true)?$value:'fr';
}
/** The language structure is the allowlist for submitted copy. */
function cw_editor_schema(string $locale): array {
    $file=__DIR__.'/dictionaries/'.cw_editor_locale($locale).'.php';
    $dict=is_file($file)?(array)require $file:array();
    $dict['site']=array('contactPhone'=>'+212 (0)5 22 36 19 88','contactEmail'=>'info@carrierweb.ma','contactAddress'=>$dict['contact']['addressValue']??'');
    $dict['dashboard']['kpiValues']=array('48/50','2','OK');
    $dict['dashboard']['previewUrl']='https://portal.carrierweb.ma';
    // These cards and navigation entries are edited through their WordPress records and menus.
    unset($dict['services']['items'],$dict['sectorsHome']['items'],$dict['testimonials']['items'],$dict['header']['nav'],$dict['header']['solutionItems'],$dict['header']['sectorItems']);
    return $dict;
}
function cw_editor_defaults(string $locale): array {return (array)apply_filters('cw_editor_defaults',cw_editor_schema($locale),$locale);}
function cw_editor_overrides(string $locale): array {$value=get_option('cw_editor_copy_'.cw_editor_locale($locale),array());return is_array($value)?$value:array();}
function cw_editor_has_override(string $path,?string $locale=null): bool {
    $node=cw_editor_overrides($locale??cw_locale());
    foreach(explode('.',$path) as $key){if(!is_array($node)||!array_key_exists($key,$node)){return false;}$node=$node[$key];}
    return true;
}
/** Preserve noneditable defaults while allowing a saved list to become empty. */
function cw_editor_merge_copy(array $base,array $overrides): array {
    foreach($overrides as $key=>$value){
        if(is_array($value)&&!array_is_list($value)&&isset($base[$key])&&is_array($base[$key])&&!array_is_list($base[$key])){
            $base[$key]=cw_editor_merge_copy($base[$key],$value);
        }else{$base[$key]=$value;}
    }
    return $base;
}
function cw_editor_dictionary(array $dict,string $locale): array {
    $schema=cw_editor_schema($locale);
    $dict['site']=$dict['site']??$schema['site'];
    foreach(array('kpiValues','previewUrl') as $key){if(!isset($dict['dashboard'][$key])){$dict['dashboard'][$key]=$schema['dashboard'][$key];}}
    foreach(cw_editor_overrides($locale) as $section=>$value){if(isset(cw_editor_sections()[$section],$schema[$section])&&is_array($value)){$value=array_intersect_key($value,$schema[$section]);$dict[$section]=cw_editor_merge_copy(is_array($dict[$section]??null)?$dict[$section]:array(),$value);}}
    return $dict;
}
add_filter('cw_dictionary','cw_editor_dictionary',10,2);
function cw_editor_media_schema(): array {
    return array('logo'=>array('label'=>'Logo du site','type'=>'image'),'hero'=>array('label'=>'Photo principale de l’accueil','type'=>'image'),'demo_video'=>array('label'=>'Vidéo de démonstration','type'=>'video'),'social_image'=>array('label'=>'Image de partage sur les réseaux sociaux','type'=>'image'));
}
function cw_editor_link_schema(): array {
    return array(
        'client_portal'=>array('label'=>'Portail client','default'=>'https://portalus1.carrierweb.com/website/web.redirect.php'),
        'whatsapp'=>array('label'=>'Contact WhatsApp','default'=>'https://wa.me/212522361988'),
        'map'=>array('label'=>'Carte — adresse Google Maps intégrable','default'=>'https://www.google.com/maps?q=39+rue+Normandie+Casablanca+Maroc&output=embed'),
        'hero_primary'=>array('label'=>'Accueil — bouton principal','default'=>'/contact'),
        'hero_secondary'=>array('label'=>'Accueil — bouton secondaire','default'=>'/solutions'),
        'cta'=>array('label'=>'Accueil — bouton de fin de page','default'=>'/contact'),
        'demo'=>array('label'=>'Boutons de demande de démonstration','default'=>'/contact'),
        'solutions'=>array('label'=>'Boutons de découverte des solutions','default'=>'/solutions'),
        'register'=>array('label'=>'Demande d’accès client','default'=>'/contact'),
        'newsletter'=>array('label'=>'Ressources — abonnement par e-mail','default'=>'mailto:info@carrierweb.ma'),
        'linkedin'=>array('label'=>'Page LinkedIn','default'=>'https://www.linkedin.com/company/carrierweb'),
    );
}
function cw_editor_shared(): array {$value=get_option('cw_editor_shared',array());return is_array($value)?$value:array();}
/** Missing or unavailable attachments retain the bundled asset. */
function cw_media_url(string $key,string $fallback=''): string {$id=absint(cw_editor_shared()['media'][$key]??0);$url=$id?wp_get_attachment_url($id):false;return $url?(string)$url:$fallback;}
function cw_media_alt(string $key,string $fallback=''): string {$id=absint(cw_editor_shared()['media'][$key]??0);$alt=$id?trim((string)get_post_meta($id,'_wp_attachment_image_alt',true)):'';return ''!==$alt?$alt:$fallback;}
/** Relative destinations follow the current language. */
function cw_editor_link(string $key,string $fallback=''): string {
    $links=cw_editor_shared()['links']??array();$url=isset($links[$key])&&is_string($links[$key])&&''!==$links[$key]?$links[$key]:$fallback;
    return ''!==$url&&'/'===$url[0]&&!str_starts_with($url,'//')?cw_url($url):$url;
}
function cw_editor_partners(): array {
    $shared=cw_editor_shared();
    if(isset($shared['partners'])&&is_array($shared['partners'])){return array_map(function($row){$id=absint($row['image']??0);return array('name'=>(string)($row['name']??''),'image'=>$id?(string)wp_get_attachment_url($id):'');},$shared['partners']);}
    return array_map(fn($name)=>array('name'=>$name,'image'=>''),array('RENAULT','SCANIA','VOLVO','HYUNDAI','ISUZU','MAN'));
}
function cw_editor_can_edit(): bool {return current_user_can('edit_others_pages');}
add_action('admin_menu',function(){add_menu_page('Contenu CarrierWeb','CarrierWeb','edit_others_pages','carrierweb-content','cw_editor_render','dashicons-edit-page',25);add_submenu_page('carrierweb-content','Textes et médias','Textes et médias','edit_others_pages','carrierweb-content','cw_editor_render');});
add_action('admin_enqueue_scripts',function(){if(isset($_GET['page'])&&'carrierweb-content'===$_GET['page']&&cw_editor_can_edit()&&current_user_can('upload_files')){wp_enqueue_media();}});
/** Copy uses known keys, bounded lists and plain text. */
function cw_editor_limit(string $value,int $length): string {return function_exists('mb_substr')?mb_substr($value,0,$length):substr($value,0,$length);}
function cw_editor_sanitize_copy($input,$schema){
    if(!is_array($schema)){
        if(!is_scalar($input)){return '';}
        $text=cw_editor_limit((string)$input,20000);
        $clean=sanitize_textarea_field($text);
        if(''===$clean){return '';}
        // Split-colour headings carry a space before or after their highlighted word.
        return (preg_match('/^\s/u',$text)?' ':'').$clean.(preg_match('/\s$/u',$text)?' ':'');
    }
    $input=is_array($input)?$input:array();$out=array();
    if(array_is_list($schema)){foreach($input as $key=>$row){if('__empty'===(string)$key||count($out)>=30){continue;}$out[]=cw_editor_sanitize_copy($row,$schema[0]??'');}}
    else{foreach($schema as $key=>$default){$out[$key]=cw_editor_sanitize_copy($input[$key]??'',$default);}}
    return $out;
}
function cw_editor_attachment_id($value,string $type): int {$id=is_scalar($value)?absint($value):0;$mime=$id?get_post_mime_type($id):'';return $id&&'attachment'===get_post_type($id)&&is_string($mime)&&str_starts_with($mime,$type.'/')?$id:0;}
function cw_editor_sanitize_link($value): string {
    if(!is_scalar($value)){return '';}$url=trim((string)$value);if(''===$url){return '';}
    if('/'===$url[0]){return str_starts_with($url,'//')?'':esc_url_raw($url);}
    return esc_url_raw($url,array('https','http','mailto','tel'));
}
add_action('admin_post_cw_editor_save',function(){
    if(!cw_editor_can_edit()){wp_die('Vous ne pouvez pas modifier ces contenus.','',array('response'=>403));}check_admin_referer('cw_editor_save');
    $locale=cw_editor_locale($_POST['cw_locale']??'');$section=is_string($_POST['cw_section']??null)?$_POST['cw_section']:'';
    if('media'!==$section&&!isset(cw_editor_sections()[$section])){wp_die('Section inconnue.','',array('response'=>400));}
    // The final marker detects PHP input truncation before saving.
    if(!isset($_POST['cw_complete'])||'1'!==$_POST['cw_complete']){wp_die('Le formulaire est incomplet. Réduisez le nombre d’éléments ou augmentez la limite du serveur avant de réessayer.','',array('response'=>400));}
    $reset=isset($_POST['cw_reset']);
    if('media'===$section){
        if($reset){delete_option('cw_editor_shared');}
        else{
            $input=isset($_POST['content'])&&is_array($_POST['content'])?wp_unslash($_POST['content']):array();$shared=array('media'=>array(),'links'=>array(),'partners'=>array());
            foreach(cw_editor_media_schema() as $key=>$field){$shared['media'][$key]=cw_editor_attachment_id($input['media'][$key]??0,$field['type']);}
            foreach(cw_editor_link_schema() as $key=>$field){$shared['links'][$key]=cw_editor_sanitize_link($input['links'][$key]??'');}
            foreach(is_array($input['partners']??null)?$input['partners']:array() as $key=>$row){if('__empty'===(string)$key||!is_array($row)||count($shared['partners'])>=30){continue;}$shared['partners'][]=array('name'=>sanitize_text_field(cw_editor_limit((string)(is_scalar($row['name']??null)?$row['name']:''),300)),'image'=>cw_editor_attachment_id($row['image']??0,'image'));}
            update_option('cw_editor_shared',$shared,false);
        }
    }else{
        $overrides=cw_editor_overrides($locale);
        if($reset){unset($overrides[$section]);}else{$input=isset($_POST['content'])?wp_unslash($_POST['content']):array();$schema=cw_editor_schema($locale);$overrides[$section]=cw_editor_sanitize_copy($input,$schema[$section]);}
        update_option('cw_editor_copy_'.$locale,$overrides,false);
    }
    wp_safe_redirect(add_query_arg(array('page'=>'carrierweb-content','cw_locale'=>$locale,'cw_section'=>$section,'saved'=>'1'),admin_url('admin.php')));exit;
});

function cw_editor_field_label(string $key): string {
    $labels=array(
        'label'=>'Surtitre / libellé','title'=>'Titre','description'=>'Description','desc'=>'Description','text'=>'Texte',
        'titlePre'=>'Titre — début','titleHl'=>'Titre — partie en couleur','titlePost'=>'Titre — fin',
        'h1Pre'=>'Titre principal — début','h1Hl'=>'Titre principal — partie en couleur','h1'=>'Titre principal',
        'titleA'=>'Titre principal — ligne 1','titleBpre'=>'Titre principal — ligne 2, début','titleBhl'=>'Titre principal — ligne 2, partie en couleur','titleC'=>'Titre principal — ligne 3',
        'subtitle'=>'Sous-titre','paragraph'=>'Paragraphe','tagline'=>'Introduction','ctaPrimary'=>'Bouton principal — texte','ctaSecondary'=>'Bouton secondaire — texte','button'=>'Bouton — texte',
        'proofs'=>'Arguments de réassurance','items'=>'Éléments','features'=>'Fonctionnalités / avantages','metrics'=>'Chiffres clés','val'=>'Valeur','link'=>'Lien — texte','q'=>'Question','a'=>'Réponse',
        'checks'=>'Points à retenir','sideBlocks'=>'Encadrés','specs'=>'Spécifications','quote'=>'Citation','author'=>'Nom / fonction du client','role'=>'Activité','company'=>'Entreprise / ville',
        'imgAlt'=>'Description de l’image pour l’accessibilité','videoAlt'=>'Description de la vidéo','sectionAria'=>'Nom de la section pour l’accessibilité',
        'kpiLabels'=>'Aperçu — libellés des indicateurs','kpiValues'=>'Aperçu — valeurs de démonstration','previewUrl'=>'Aperçu — adresse affichée',
        'line'=>'Phrase d’introduction','note'=>'Note','badge'=>'Badge','breadcrumb'=>'Fil d’Ariane','nav'=>'Navigation principale',
        'solutionItems'=>'Solutions — libellés de repli','sectorItems'=>'Secteurs — libellés de repli','blurb'=>'Texte de présentation',
        'rights'=>'Copyright','legal'=>'Mentions légales — libellé','privacy'=>'Confidentialité — libellé',
        'rootTitle'=>'Accueil — titre pour les moteurs de recherche','rootDescription'=>'Accueil — description pour les moteurs de recherche','ogTitle'=>'Partage social — titre','ogDescription'=>'Partage social — description',
        'solutions'=>'Solutions','sectors'=>'Secteurs','why'=>'Pourquoi CarrierWeb','references'=>'Références','resources'=>'Ressources','contact'=>'Contact','clientArea'=>'Espace Client','login'=>'Connexion','register'=>'Demande d’accès',
        'home'=>'Accueil','fleetOptions'=>'Tailles de flotte proposées','name'=>'Nom','image'=>'Logo','contactPhone'=>'Téléphone de contact','contactEmail'=>'Adresse e-mail de contact','contactAddress'=>'Adresse de contact','footerBlurb'=>'Présentation de l’entreprise','linkedinUrl'=>'Page LinkedIn','clientPortalUrl'=>'Portail client',
    );
    if(isset($labels[$key])){return $labels[$key];}
    $words=preg_split('/\s+/',strtolower((string)preg_replace('/(?<!^)([A-Z])/',' $1',$key)));
    $map=array('title'=>'titre','desc'=>'description','label'=>'libellé','ph'=>'exemple affiché','aria'=>'accessibilité','cta'=>'bouton','pre'=>'début','hl'=>'partie en couleur','post'=>'fin','body'=>'texte','note'=>'note','link'=>'lien','nav'=>'navigation','support'=>'assistance','phone'=>'téléphone','email'=>'e-mail','address'=>'adresse','form'=>'formulaire','info'=>'coordonnées','client'=>'client','box'=>'encadré','wa'=>'WhatsApp','sent'=>'envoi réussi','success'=>'confirmation','send'=>'envoyer','another'=>'autre message','submit'=>'bouton d’envoi','sending'=>'envoi en cours','read'=>'lecture','min'=>'minutes','newsletter'=>'newsletter','perf'=>'performance','presence'=>'présence locale','side'=>'encadré','refs'=>'références','cross'=>'autre rubrique','related'=>'contenus liés','open'=>'ouvrir','portal'=>'portail','prev'=>'précédent','next'=>'suivant','go'=>'aller','to'=>'à','testimonials'=>'témoignages','help'=>'aide','security'=>'sécurité','by'=>'par','availability'=>'horaires','value'=>'valeur','field'=>'champ','message'=>'message','name'=>'nom','company'=>'entreprise','fleet'=>'flotte','vehicle'=>'véhicule','route'=>'trajet','tag'=>'étiquette','demo'=>'démonstration','data'=>'données','learn'=>'découvrir','more'=>'plus','request'=>'demander','long'=>'version longue','back'=>'retour','top'=>'haut','theme'=>'thème','light'=>'clair','dark'=>'sombre','skip'=>'aller','content'=>'contenu','main'=>'principal','expert'=>'expert','talk'=>'parler','free'=>'gratuit','all'=>'toutes','technology'=>'technologie','discover'=>'découvrir','logo'=>'logo','lang'=>'langue','switch'=>'sélecteur','french'=>'française','only'=>'uniquement','takeaways'=>'à retenir','continue'=>'poursuivre','reading'=>'la lecture','features'=>'fonctionnalités','specs'=>'spécifications','faq'=>'questions fréquentes','seo'=>'référencement','expertise'=>'expertise','recommended'=>'recommandées','challenges'=>'enjeux','use'=>'cas','cases'=>'d’usage','see'=>'voir','checks'=>'points à retenir','p1'=>'paragraphe 1','p2'=>'paragraphe 2');
    return ucfirst(implode(' — ',array_map(fn($part)=>$map[$part]??$part,$words)));
}
function cw_editor_empty_shape($schema){return is_array($schema)?array_map('cw_editor_empty_shape',$schema):'';}
function cw_editor_render_fields($value,$schema,string $name,string $label): void {
    if(!is_array($schema)){
        $id='cw-'.preg_replace('/[^A-Za-z0-9_-]+/','-',$name);$value=is_scalar($value)?(string)$value:'';
        echo '<p class="cw-field"><label for="'.esc_attr($id).'"><strong>'.esc_html($label).'</strong></label><br>';
        if(strlen((string)$schema)>95){echo '<textarea class="large-text" dir="auto" rows="3" id="'.esc_attr($id).'" name="'.esc_attr($name).'">'.esc_textarea($value).'</textarea>';}
        else{echo '<input class="large-text" dir="auto" type="text" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr($value).'">';}
        echo '</p>';return;
    }
    $value=is_array($value)?$value:$schema;
    if(array_is_list($schema)){
        $token='__CW_'.md5($name).'__';
        echo '<fieldset class="cw-list"><legend><h2>'.esc_html($label).'</h2></legend><input type="hidden" name="'.esc_attr($name.'[__empty]').'" value="1"><div class="cw-rows">';
        foreach($value as $index=>$row){cw_editor_render_row($row,$schema[0]??'',$name,(string)$index,$label);}
        echo '</div><template class="cw-template" data-token="'.esc_attr($token).'">';
        cw_editor_render_row(cw_editor_empty_shape($schema[0]??''),$schema[0]??'',$name,$token,$label);
        echo '</template><button type="button" class="button cw-add">Ajouter un élément</button></fieldset>';
    }else{
        echo '<fieldset class="cw-group"><legend><h2>'.esc_html($label).'</h2></legend>';
        foreach($schema as $key=>$default){cw_editor_render_fields($value[$key]??$default,$default,$name.'['.$key.']',cw_editor_field_label((string)$key));}
        echo '</fieldset>';
    }
}
function cw_editor_render_row($value,$schema,string $name,string $index,string $label): void {
    echo '<div class="cw-row" data-row-index="'.esc_attr($index).'"><div class="cw-row-actions"><strong class="cw-row-number">Élément</strong><button type="button" class="button cw-up" aria-label="Monter cet élément">↑</button><button type="button" class="button cw-down" aria-label="Descendre cet élément">↓</button><button type="button" class="button cw-remove">Retirer</button></div>';
    cw_editor_render_fields($value,$schema,$name.'['.$index.']',is_array($schema)?$label:'Texte');echo '</div>';
}
function cw_editor_render_picker(string $name,int $id,string $type,string $label): void {
    $url=$id?wp_get_attachment_url($id):'';
    echo '<div class="cw-picker" data-type="'.esc_attr($type).'"><p><strong>'.esc_html($label).'</strong></p><input type="hidden" name="'.esc_attr($name).'" value="'.esc_attr((string)$id).'"><p class="cw-media-preview">';
    if($url){echo 'image'===$type?'<img src="'.esc_url($url).'" alt="" style="max-width:220px;max-height:110px">':esc_html(basename($url));}else{echo 'Média du thème par défaut';}
    echo '</p><button type="button" class="button cw-media-select">Choisir dans la médiathèque</button> <button type="button" class="button cw-media-clear">Utiliser le média par défaut</button></div>';
}
function cw_editor_render_partner(array $row,string $index): void {
    echo '<div class="cw-row" data-row-index="'.esc_attr($index).'"><div class="cw-row-actions"><strong class="cw-row-number">Marque</strong><button type="button" class="button cw-up" aria-label="Monter cette marque">↑</button><button type="button" class="button cw-down" aria-label="Descendre cette marque">↓</button><button type="button" class="button cw-remove">Retirer</button></div>';
    cw_editor_render_fields($row['name']??'','','content[partners]['.$index.'][name]','Nom de la marque');
    cw_editor_render_picker('content[partners]['.$index.'][image]',absint($row['image']??0),'image','Logo — facultatif, le nom s’affiche sinon');echo '</div>';
}
function cw_editor_render(): void {
    if(!cw_editor_can_edit()){return;}
    $locale=cw_editor_locale($_GET['cw_locale']??'fr');$sections=cw_editor_sections();
    $section=isset($_GET['cw_section'])&&is_string($_GET['cw_section'])?$_GET['cw_section']:'hero';
    if('media'!==$section&&!isset($sections[$section])){$section='hero';}
    $defaults=cw_editor_defaults($locale);$overrides=cw_editor_overrides($locale);
    echo '<div class="wrap cw-editor"><h1>Contenu CarrierWeb</h1><p>Choisissez une rubrique, modifiez son contenu puis enregistrez. Les changements apparaissent sur le site après actualisation. Chaque langue se modifie séparément.</p>';
    if(isset($_GET['saved'])){echo '<div class="notice notice-success is-dismissible"><p>Contenu enregistré.</p></div>';}
    echo '<p>Les fiches et leurs traductions se gèrent dans <a href="'.esc_url(admin_url('edit.php?post_type=solution')).'">Solutions</a>, <a href="'.esc_url(admin_url('edit.php?post_type=secteur')).'">Secteurs</a>, <a href="'.esc_url(admin_url('edit.php?post_type=article')).'">Articles</a> et <a href="'.esc_url(admin_url('edit.php?post_type=temoignage')).'">Témoignages</a>. Les rubriques de mise en page restent fixes.</p>';
    echo '<nav class="nav-tab-wrapper" aria-label="Langue du contenu">';
    foreach(array('fr'=>'Français','en'=>'English','ar'=>'العربية') as $slug=>$name){echo '<a class="nav-tab'.($slug===$locale?' nav-tab-active':'').'" href="'.esc_url(add_query_arg(array('page'=>'carrierweb-content','cw_locale'=>$slug,'cw_section'=>$section),admin_url('admin.php'))).'">'.esc_html($name).'</a>';}
    echo '</nav><div class="cw-editor-layout"><nav class="cw-section-nav" aria-label="Rubrique à modifier">';
    foreach(array('media'=>'Médias, liens et logos partenaires')+$sections as $key=>$label){echo '<a'.($key===$section?' aria-current="page"':'').' href="'.esc_url(add_query_arg(array('page'=>'carrierweb-content','cw_locale'=>$locale,'cw_section'=>$key),admin_url('admin.php'))).'">'.esc_html($label).'</a>';}
    echo '</nav><form method="post" action="'.esc_url(admin_url('admin-post.php')).'" class="cw-content-form"><h2>'.esc_html('media'===$section?'Médias et liens partagés entre les trois langues':$sections[$section]).'</h2>';
    wp_nonce_field('cw_editor_save');
    echo '<input type="hidden" name="action" value="cw_editor_save"><input type="hidden" name="cw_locale" value="'.esc_attr($locale).'"><input type="hidden" name="cw_section" value="'.esc_attr($section).'">';
    if('media'===$section){
        $shared=cw_editor_shared();
        echo '<p>Les médias se choisissent dans la médiathèque WordPress. Ajoutez une description alternative aux images dans la médiathèque.</p>';
        foreach(cw_editor_media_schema() as $key=>$field){cw_editor_render_picker('content[media]['.$key.']',absint($shared['media'][$key]??0),$field['type'],$field['label']);}
        echo '<h2>Destinations des liens</h2><p>Pour une page du site, utilisez son chemin, par exemple <code>/contact</code>. La langue suivra celle du visiteur. Pour un service externe, utilisez son adresse complète.</p>';
        foreach(cw_editor_link_schema() as $key=>$field){cw_editor_render_fields($shared['links'][$key]??$field['default'],$field['default'],'content[links]['.$key.']',$field['label']);}
        echo '<fieldset class="cw-list cw-partners"><legend><h2>Marques compatibles / partenaires</h2></legend><input type="hidden" name="content[partners][__empty]" value="1"><div class="cw-rows">';
        $rows=$shared['partners']??array_map(fn($name)=>array('name'=>$name,'image'=>0),array('RENAULT','SCANIA','VOLVO','HYUNDAI','ISUZU','MAN'));
        foreach($rows as $index=>$row){cw_editor_render_partner($row,(string)$index);}
        echo '</div><template class="cw-template" data-token="__CW_PARTNER__">';cw_editor_render_partner(array('name'=>'','image'=>0),'__CW_PARTNER__');echo '</template><button type="button" class="button cw-add">Ajouter une marque</button></fieldset>';
    }else{
        echo '<p>Un champ vide masque son texte. Les listes peuvent être réordonnées, complétées ou raccourcies. Le bouton de retour au contenu d’origine retire les modifications de cette rubrique et de cette langue.</p>';
        cw_editor_render_fields($overrides[$section]??$defaults[$section],cw_editor_schema($locale)[$section],'content',$sections[$section]);
    }
    echo '<input type="hidden" name="cw_complete" value="1"><p class="submit"><button type="submit" class="button button-primary">Enregistrer les modifications</button> <button type="submit" name="cw_reset" value="1" class="button cw-reset">Revenir au contenu d’origine</button></p></form></div></div>';
    cw_editor_admin_assets();
}
function cw_editor_admin_assets(): void {
?>
<style>
.cw-editor-layout{display:grid;grid-template-columns:250px minmax(0,850px);gap:25px;margin-top:20px}.cw-section-nav a{display:block;padding:8px 10px;border-bottom:1px solid #ddd;text-decoration:none}.cw-section-nav [aria-current]{background:#2271b1;color:#fff}.cw-content-form{background:#fff;padding:22px;border:1px solid #dcdcde}.cw-group,.cw-list{margin:15px 0;padding:10px 14px;border:1px solid #dcdcde}.cw-group legend h2,.cw-list legend h2{font-size:14px;margin:0}.cw-row{padding:12px;margin:12px 0;border:1px solid #c3c4c7;background:#f6f7f7}.cw-row-actions{display:flex;gap:6px;align-items:center}.cw-row-actions strong{margin-right:auto}.cw-field input,.cw-field textarea{margin-top:6px}.cw-picker{padding-bottom:18px;border-bottom:1px solid #ddd;margin-bottom:18px}.cw-content-form .submit{position:sticky;bottom:0;background:#fff;border-top:1px solid #ddd;padding:15px 0;margin-bottom:0}@media(max-width:900px){.cw-editor-layout{grid-template-columns:1fr}.cw-section-nav{display:flex;flex-wrap:wrap;gap:5px}}
</style>
<script>
(function(){
'use strict';
const root=document.querySelector('.cw-editor');if(!root)return;
function refresh(list){const rows=list.querySelector(':scope > .cw-rows');if(!rows)return;Array.from(rows.children).forEach((row,i)=>{const label=row.querySelector('.cw-row-number');if(label)label.textContent='Élément '+(i+1);});}
root.querySelectorAll('.cw-list').forEach(refresh);
root.addEventListener('click',function(event){
 const target=event.target.closest('button');if(!target)return;
 if(target.classList.contains('cw-reset')&&!window.confirm('Revenir au contenu d’origine pour cette rubrique ?')){event.preventDefault();return;}
 if(target.classList.contains('cw-add')){
  const list=target.closest('.cw-list'),rows=list.querySelector(':scope > .cw-rows'),template=list.querySelector(':scope > .cw-template');
  if(rows.children.length>=30){window.alert('Cette liste est limitée à 30 éléments.');return;}
  const next=Array.from(rows.children).reduce((n,row)=>Math.max(n,Number(row.dataset.rowIndex)||0),-1)+1;
  const holder=document.createElement('template');holder.innerHTML=template.innerHTML.split(template.dataset.token).join(String(next));rows.append(holder.content.cloneNode(true));refresh(list);
 }
 if(target.classList.contains('cw-remove')){const row=target.closest('.cw-row'),list=row.closest('.cw-list');row.remove();refresh(list);}
 if(target.classList.contains('cw-up')||target.classList.contains('cw-down')){const row=target.closest('.cw-row'),rows=row.parentElement;if(target.classList.contains('cw-up')&&row.previousElementSibling)rows.insertBefore(row,row.previousElementSibling);if(target.classList.contains('cw-down')&&row.nextElementSibling)rows.insertBefore(row.nextElementSibling,row);refresh(row.closest('.cw-list'));}
 if(target.classList.contains('cw-media-clear')){const picker=target.closest('.cw-picker');picker.querySelector('input').value='0';picker.querySelector('.cw-media-preview').textContent='Média du thème par défaut';}
 if(target.classList.contains('cw-media-select')){
  if(!window.wp||!wp.media){window.alert('La médiathèque est réservée aux comptes autorisés à téléverser des fichiers.');return;}
  const picker=target.closest('.cw-picker'),type=picker.dataset.type;
  const frame=wp.media({title:type==='video'?'Choisir une vidéo':'Choisir une image',button:{text:'Utiliser ce média'},library:{type:type},multiple:false});
  frame.on('select',()=>{const media=frame.state().get('selection').first().toJSON();if(media.type!==type)return;picker.querySelector('input').value=String(media.id);const preview=picker.querySelector('.cw-media-preview');preview.textContent='';if(type==='image'){const img=document.createElement('img');img.src=media.sizes&&media.sizes.thumbnail?media.sizes.thumbnail.url:media.url;img.alt='';img.style.maxWidth='220px';img.style.maxHeight='110px';preview.append(img);}else{preview.textContent=media.filename||media.url;}});frame.open();
 }
});
})();
</script>
<?php
}
