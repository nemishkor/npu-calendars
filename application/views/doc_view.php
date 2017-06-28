<h2>Widgets</h2>
<p>For add widget in view run in view file method <code>widget(string $name, array $params = null)</code><br>For example:</p>
<pre>$this->widget('toolbar', array("add" => '1'));</pre>
<p>Widgets placed in '/application/widgets/'. File name must be '$name.php'. Example widget class with minimal requirements:</p>
<pre>&lt;?php
class Widget_Breadcrumbs extends Widget{
	
	function display(){
		echo 'some widget output';
	}
	
}
?&gt;</pre>
<p>Where <code>Toolbar</code> is <code>$name</code> (name of widget)</p>
<p>Just create <code>Widget_$name</code> class that will be extends from <code>Widget</code> class and override <code>display</code> function</p>
