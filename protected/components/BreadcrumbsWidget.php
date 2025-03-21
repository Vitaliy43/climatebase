<?php
/**
 * CBreadcrumbs class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CBreadcrumbs displays a list of links indicating the position of the current page in the whole website.
 *
 * For example, breadcrumbs like "Home > Sample Post > Edit" means the user is viewing an edit page
 * for the "Sample Post". He can click on "Sample Post" to view that page, or he can click on "Home"
 * to return to the homepage.
 *
 * To use CBreadcrumbs, one usually needs to configure its {@link links} property, which specifies
 * the links to be displayed. For example,
 *
 * <pre>
 * $this->widget('zii.widgets.CBreadcrumbs', array(
 *     'links'=>array(
 *         'Sample post'=>array('post/view', 'id'=>12),
 *         'Edit',
 *     ),
 * ));
 * </pre>
 *
 * Because breadcrumbs usually appears in nearly every page of a website, the widget is better to be placed
 * in a layout view. One can define a property "breadcrumbs" in the base controller class and assign it to the widget
 * in the layout, like the following:
 *
 * <pre>
 * $this->widget('zii.widgets.CBreadcrumbs', array(
 *     'links'=>$this->breadcrumbs,
 * ));
 * </pre>
 *
 * Then, in each view script, one only needs to assign the "breadcrumbs" property as needed.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CBreadcrumbs.php 2799 2011-01-01 19:31:13Z qiang.xue $
 * @package zii.widgets
 * @since 1.1
 */
class BreadcrumbsWidget extends CWidget
{
	/**
	 * @var string the tag name for the breadcrumbs container tag. Defaults to 'div'.
	 */
	public $tagName='div';
	/**
	 * @var array the HTML attributes for the breadcrumbs container tag.
	 */
	public $htmlOptions=array('class'=>'breadcrumbs');
	/**
	 * @var boolean whether to HTML encode the link labels. Defaults to true.
	 */
	public $encodeLabel=true;
	/**
	 * @var string the first hyperlink in the breadcrumbs (called home link).
	 * If this property is not set, it defaults to a link pointing to {@link CWebApplication::homeUrl} with label 'Home'.
	 * If this property is false, the home link will not be rendered.
	 */
	public $homeLink;
	/**
	 * @var array list of hyperlinks to appear in the breadcrumbs. If this property is empty,
	 * the widget will not render anything. Each key-value pair in the array
	 * will be used to generate a hyperlink by calling CHtml::link(key, value). For this reason, the key
	 * refers to the label of the link while the value can be a string or an array (used to
	 * create a URL). For more details, please refer to {@link CHtml::link}.
	 * If an element's key is an integer, it means the element will be rendered as a label only (meaning the current page).
	 *
	 * The following example will generate breadcrumbs as "Home > Sample post > Edit", where "Home" points to the homepage,
	 * "Sample post" points to the "index.php?r=post/view&id=12" page, and "Edit" is a label. Note that the "Home" link
	 * is specified via {@link homeLink} separately.
	 *
	 * <pre>
	 * array(
	 *     'Sample post'=>array('post/view', 'id'=>12),
	 *     'Edit',
	 * )
	 * </pre>
	 */
	public $links=array();
	public $home_link=SITE_PATH;
	
	/**
	 * @var string the separator between links in the breadcrumbs. Defaults to ' &raquo; '.
	 */
	public $separator=' &raquo; ';
	
	/**
	 * @var string Массив html-опций для ссылок.
	 */
	public $link_options=array(
		'onclick'=>'ajax_link(this.href);return false'
		);
	

	/**
	 * Renders the content of the portlet.
	 */
	 
	 
	 
	public function run()
	{
		if(empty($this->links))
			return;
		if(isset($_REQUEST['lang']) and $_REQUEST['lang']=='en'){
			$root_name='Main';
			$add_link='?lang=en';

		}
		else{
			$root_name='Главная';
			$add_link='';

		}
		echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
		$links=array();
		if($this->homeLink===null)
			$links[]=CHtml::link(Yii::t('zii',$root_name),Yii::app()->homeUrl.$add_link);
		else if($this->homeLink!==false)
			$links[]=$this->homeLink;
		foreach($this->links as $label=>$url)
		{
				$url.=$add_link;
			if(is_string($label) || is_array($url)){
				if(AJAX_LINK)
					$links[]=CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $url,$this->link_options);
				else
					$links[]=CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $url);

				
			}
			else{
				$links[]='<span>'.($this->encodeLabel ? CHtml::encode($url) : $url).'</span>';

			}
		}
		echo implode($this->separator,$links);
		echo CHtml::closeTag($this->tagName);
		
	}
}