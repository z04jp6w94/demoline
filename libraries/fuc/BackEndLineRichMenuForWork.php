<?php

class BackEndLineRichMenuForWork {

    private $accessToken;

    function __construct($accessToken) {
        $this->accessToken = $accessToken;
    }

    function __destruct() {
        
    }

    public function CreateMenuId($title, $area_content) {
        $sh = <<< EOF
  curl -X POST \
  -H 'Authorization: Bearer $this->accessToken' \
  -H 'Content-Type:application/json' \
  -d '{"size": {"width": 2500,"height": 1686},"selected": false,"name": "$title","chatBarText": "$title","areas": [$area_content]}' https://api.line.me/v2/bot/richmenu;
EOF;

        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['richMenuId'])) {
            return $result['richMenuId'];
        } else {
            return FALSE;
        }
    }

    public function UploadMenuPhoto($richMenuId, $imagePath) {
        $sh = <<< EOF
  curl -v -X POST https://api.line.me/v2/bot/richmenu/$richMenuId/content \
  -H "Authorization: Bearer $this->accessToken" \
  -H "Content-Type: image/jpeg" \
  -T $imagePath 
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['message'])) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function LinkMenuToUser($richMenuId, $User) {
        $sh = <<< EOF
  curl -X POST \
  -H 'Authorization: Bearer $this->accessToken' \
  -H 'Content-Length: 0' \
  https://api.line.me/v2/bot/user/$User/richmenu/$richMenuId
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['message'])) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function DeleteRichMenu($richMenuId) {
        $sh = <<< EOF
  curl -X DELETE \
  -H 'Authorization: Bearer $this->accessToken' \
  https://api.line.me/v2/bot/richmenu/$richMenuId
EOF;
        $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
        if (isset($result['message'])) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

?>