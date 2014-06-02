<?php
$db 	= new SQLite3( 'fingertext.db3');
$query 	= $db->query( 'SELECT * FROM snippets');

while( $row = $query->fetchArray( SQLITE3_ASSOC ) )
{
	$snippet 	= preg_replace( '/\[>END<\]/', '', $row[ 'snippet'] );
	$matches 	= array();
	$pos		= 0;
	$count		= 0;

	preg_match_all( '/\$\[!\[(.*?)]!\]/', $snippet, $matches );

	foreach( $matches[ 0 ] as $key => $match )
	{
		$count		= $count + 1;
		$string		= $matches[ 1 ][ $key ];
		$pos 		= strpos( $snippet, $match, $pos );
		$replace 	= $string == "" ? '${' . $count . '}' : '${' . $count . ':' . $string . '}';
		$snippet 	= substr_replace( $snippet, $replace, $pos, strlen( $match ) );
	}

	$snippet		= str_replace( ' $ ', ' \$ ', $snippet );

$xml = <<<EOD
<snippet>
    <content><![CDATA[{$snippet}]]></content>
    <tabTrigger>{$row['tag']}</tabTrigger>
    <scope>source.mivascript</scope>
</snippet>
EOD;

	if ( !file_put_contents( $row['tag'].'.sublime-snippet', $xml ) )
	{
		die( "Failed creating file" );
	}
}
?>
