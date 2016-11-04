usage:

```
require_once 'CKIP.php';
$ckip_client = new CKIP( $server, $port, $username, $password);
$ckip_client ->query( $querystring);
$ckip_client ->getTerm();
$ckip_client ->getSents();
```