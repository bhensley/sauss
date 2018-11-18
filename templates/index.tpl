<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title>SAUSS: Simplify Your Life</title>
    <meta http-equiv="content-type: text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="client/main.css" />
    {literal}
    <script type="text/javascript">
    //<![CDATA[

    window.onkeypress = function (event)
    {
      if (event.keyCode == 13)
      {
        document.forms[0].submit ();
      }
    }
    
    //]]>
    </script>
    {/literal}
  </head>
  
  <body>
	  <div id="container">
      <div id="content">
        {if isset($has_error) && $has_error}
          {include file="error.tpl"}
        {else}
          {if isset($page)}
            {include file="$page.tpl"}
          {else}
          <form method="post" action="index.php">
            <p>
              <input type="text" name="url" class="text" /><br />
              Enter the URL to be shortened and hit enter.
            </p>
          </form>
          {/if}
        {/if}
      </div>
	  </div>
  </body>
</html>