<html>
    <head>
        <title>Facebook Search</title>
        <meta charset="UTF-8" >
        <style type = "text/css">
            .myForm {
                border-style: solid;
                border-width: 1px;
                border-color: black;
                background-color:lightgray;
                width: 500px;
                height: 150px;
                margin-top: auto;
                margin-left: auto;
                margin-right: auto;
                padding: 5px 10px 0px 10px;
            }
    
            .head {
                font-size: 32;
                font-weight: bold;
                font-style: italic;
                text-align: center;
            }
            
            #location {
                visibility: hidden;   
            }
            
            .bottons {
                margin-left: 60px;
            }

            #result {
                display:block;
            }
            
            table {
                width: 600px;
                height: auto;
                margin-top: auto;
                margin-left: auto;
                margin-right: auto;
                margin-bottom: auto;
            }
            
            th {
                text-align: left;
                background-color: lightgray;
            }
            
            table, th, td {
                border: 2px solid gray;
                border-collapse: collapse;
            }
            
            .notFound {
                width: 600px;
                border: 1px solid gray;
                text-align: center;
                background-color: lightgray;
                margin-top: auto;
                margin-left: auto;
                margin-right: auto;
            }
            
            .detail_h {
                text-align: center;
                width: 600px;
                height: 20px;
                background-color: lightgray;
                margin-top: auto;
                margin-left: auto;
                margin-right: auto;
                display: block;
            }

            #album {
                display: none;
            }

            #post {
                display: none;
            } 
        </style>
        
        <script type = "text/javascript">
            function resetAll() {
                document.form.type.options[0].selected = "selected";
                document.getElementById('searchResult').innerHTML = '';
                document.form.key.value = '';
                document.form.zip.value = '';
                document.form.distance.value = '';
                document.getElementById("location").style.visibility = 'hidden';
            }

            function displayLocation() {
                document.getElementById("location").style.visibility = 'visible';
            }

            function hideLocation() {
                document.getElementById("location").style.visibility = 'hidden';
            }

            function display(obj) {
                if(obj.options[obj.selectedIndex].value == "place") {
                    displayLocation();
                } else {
                    hideLocation();
                }
            }

            function showAlbum() {
                var p = document.getElementById('post');
                if (p) {
                   document.getElementById('post').style.display = 'none'; 
                }
                if (document.getElementById('album').style.display == 'block') {
                    document.getElementById('album').style.display = 'none';
                } else {
                    document.getElementById('album').style.display = 'block';
                }
            }

            function showPost() {
                var p = document.getElementById('album');
                if (p) {
                    document.getElementById('album').style.display = 'none';
                }
                if (document.getElementById('post').style.display == 'block') {
                    document.getElementById('post').style.display = 'none';
                } else {
                    document.getElementById('post').style.display = 'block';
                }   
            }

            function showPic(obj) {
                if (document.getElementById(obj).style.display == 'block') {
                    document.getElementById(obj).style.display = 'none';
                } else {
                    document.getElementById(obj).style.display = 'block';
                }
            }
        </script>          
    </head>
    
    <body>
        <form name = "form" method = "GET" action = "">
            <div class = "myForm">
                <div class = "head">Facebook Search</div>
                <hr />
                <div>
                    Keyword
                    <input type = "text" name = "key" style = "width: 150px" value = "<?php echo isset($_GET["key"]) ? $_GET["key"] : ""; ?>" required oninvalid="setCustomValidity('This cannot be left empty')" oninput="setCustomValidity('')"/>
                </div>
                <div>
                    Type:
                    <select name = "type" style = "width:100px; margin-left:23px" onchange = "display(this)">
                    <option value = "user"<?php echo isset($_GET["type"]) && $_GET["type"] == "user" ? "selected" : "" ?>>Users</option>
                    <option value = "page"<?php echo isset($_GET["type"]) && $_GET["type"] == "page" ? "selected" : "" ?>>Pages</option>
                    <option value = "event"<?php echo isset($_GET["type"]) && $_GET["type"] == "event" ? "selected" : "" ?>>Events</option>
                    <option value = "group"<?php echo isset($_GET["type"]) && $_GET["type"] == "group" ? "selected" : "" ?>>Groups</option>
                    <option value = "place"<?php echo isset($_GET["type"]) && $_GET["type"] == "place" ? "selected" : "" ?>>Places</option>
                    </select>
                </div>
                <div id = "location">            
                    Location
                    <input type = "text" name = "zip" style = "width: 150px; margin-left: 3px" value = "<?php echo isset($_GET["zip"]) ? $_GET["zip"] : ""; ?>">
                    Distance(meter)
                    <input type = "text" name = "distance" style = "width: 150px" value ="<?php echo isset($_GET["distance"]) ? $_GET["distance"] : ""; ?>">
                </div>
                <div>
                    <input type = "submit" name = "submit" value = "Search" style = "margin-left: 60px">
                    <input type = "button" name = "reset" value = "Clear" onClick = "resetAll()">
                </div>
            </div>
        </form>
        
        <div id = 'searchResult'>
            
            <?php
    
            date_default_timezone_set("America/Los_Angeles");
            require_once __DIR__ . '/php-graph-sdk-5.0.0/src/Facebook/autoload.php';
            $fb = new Facebook\Facebook([
                'app_id' => '248335022303052',
                'app_secret' => '8e254423c15ede506380a9123b1394ea',
                'default_graph_version' => 'v2.8',
                'default_access_token' => 'EAADh3ADKJ0wBALDA5eDgCELhaGeB0gHttq2qpw4rmPZBzOBRjvD05ez2ZAku7khx24QI9OZC1ZAwF1BpVUHxndLZA4VYsa5RAdOFfMO5FOuf9bsDC0mC7UqcqM9w7K0ZB5NTOI4rjVVuQLTE5MFGSIkZBuVtHbci8oZD',
            ]);

            if (isset($_GET['submit'])):
                $length = strlen(trim($_GET["key"]));
                if(!empty($_GET["type"]) && $length > 0){

                    $typeOfEntity = "&type=";
                    $typeOfField = "&fields=id,name,picture.width(700).height(700)";
                    $q="";
                    $google_api_key = "&key=AIzaSyDyB6xNPmkVIfH3gvsr1pJSJLus1oZjqcM";

                    if(isset($_GET["type"]) && $_GET["type"] == "place" && isset($_GET["key"])){
                        echo "<script type = 'text/javascript'>";
                        echo "displayLocation();";
                        echo "</script>";
                        $temp = trim(strtolower(htmlentities($_GET['key'])));
                        $keyarray = preg_split("/\s+/", $temp);
                        $address = "";
                        $i = 0;
                        foreach ($keyarray as $item) {
                            if($i == 0) {
                                $address .= $item;
                            } else {
                                $address .= '+';
                                $address .= $item;
                            }
                            $i = 1;
                        }
                        if (isset($_GET["zip"])) {
                            $address .= '+';
                            $address .= trim(strtolower(htmlentities($_GET['zip'])));
                        }

                        $googleURL = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address.$google_api_key;
                        $google_response = file_get_contents($googleURL);
                        $json_array = json_decode($google_response, true);
                        $lat = $json_array['results'][0]['geometry']['location']['lat'];
                        $lng = $json_array['results'][0]['geometry']['location']['lng'];
                        $q="q=".rawurlencode(trim(strtolower($_GET['key'])));    
                    }

                    if(isset($_GET["type"]) && $_GET["type"] == "user" && isset($_GET["key"])){
                        $typeOfEntity .= "user";
                        $q= "q=".rawurlencode(trim(strtolower($_GET['key'])));
                    }
                    if(isset($_GET["type"]) && $_GET["type"] == "page" && isset($_GET["key"])){
                        $typeOfEntity .= "page";
                        $q= "q=".rawurlencode(trim(strtolower($_GET['key'])));
                    }
                    if(isset($_GET["type"]) && $_GET["type"] == "group" && isset($_GET["key"])){
                        $typeOfEntity .= "group";
                        $q= "q=".rawurlencode(trim(strtolower($_GET['key'])));
                    }
                    if(isset($_GET["type"]) && $_GET["type"] == "event" && isset($_GET["key"])){
                        $typeOfEntity .= "event";
                        $q= "q=".rawurlencode(trim(strtolower($_GET['key'])));
                    }

                    if ($_GET["type"] == "user" or $_GET["type"] == "page" or $_GET["type"] == "group") {
                        $targetURL = '/search?'.$q.$typeOfEntity.$typeOfField;
                    }       
                    if($_GET["type"] == "event") {
                        $targetURL = '/search?'.$q.$typeOfEntity.$typeOfField.',place';
                    }
                    if($_GET["type"] == "place") {
                        $typeOfEntity .= "place";
                        if(isset($_GET["distance"])) {
                            $distance = $_GET["distance"];
                        } else {
                            $distance = "1000";
                        } 
                        $targetURL='/search?'.$q.$typeOfEntity."&center=".$lat.",".$lng."&distance=".$distance.$typeOfField;
                    }

                    $response = $fb->get($targetURL);        
                    $graphEdge = $response->getGraphEdge();

                    echo "<div id = 'result'>";            
                    if (count($graphEdge->asArray()) == 0) {
                        echo "<table>";
                        echo "<tr><td class = 'notFound'>No Records has been found</td></tr>";
                        echo "</table>";
                    } else {
                        echo "<table>";
                        if($_GET["type"] == "user" or $_GET["type"] == "page" or $_GET["type"] == "group" or $_GET["type"] == "place"){
                            echo "<tr><th>Profile Photo</th><th>Name</th><th>Details</th></tr>";
                            foreach ($graphEdge as $graphNode) {
                                $ele = $graphNode->asArray();
                                echo "<tr><td><a href = ".$ele['picture']['url']." target = 'view window'><img src=".$ele['picture']['url']." width='40px' height='30px'></a></td><td>".$ele['name']."</td><td><a href=".$_SERVER['PHP_SELF']."?id=".$ele['id']."&key=".$_GET['key']."&type=".$_GET['type']."&zip=".$_GET['zip']."&distance=".$_GET['distance'].">Details</a></td></tr>";
                            }       
                        }

                        if($_GET["type"] == "event") {
                            echo "<tr><th>Profile Photo</th><th>Name</th><th>Place</th></tr>";
                            foreach ($graphEdge as $graphNode) {
                                $ele = $graphNode->asArray();
                                if (!isset($ele['place']['name'])) {
                                    echo "<tr><td><a href = ".$ele['picture']['url']." target = 'view window'><img src=".$ele['picture']['url']." width='40px' height='30px'>"."</a></td><td>".$ele['name']."</td><td>Not found</td></tr>";
                                } else {
                                    echo "<tr><td><a href = ".$ele['picture']['url']." target = 'view window'><img src=".$ele['picture']['url']." width='40px' height='30px'>"."</a></td><td>".$ele['name']."</td><td>".$ele['place']['name']."</td></tr>";
                                }
                            }

                        }
                        echo "</table>";
                    }
                    echo "</div>";
                }
            endif;
                
            if (isset($_GET['id'])) {
                if(isset($_GET["type"]) && $_GET["type"] == "place"){
                    echo "<script type = 'text/javascript'>";
                    echo "displayLocation();";
                    echo "</script>";
                }
                
                $url = "/".$_GET["id"]."?fields=id,name,picture.width(700).height(700),albums.limit(5){name,photos.limit(2){name, picture}},posts.limit(5)";
                $response = $fb->get($url);
                $graphNode = $response->getDecodedBody();

                if(!isset($graphNode['albums'])) {
                    echo "<div class = 'notFound'>";
                    echo "No Albums has been found";
                    echo "</div>";
                } else {
                    echo "<div class = 'detail_h'>";
                    echo "<a href = '#' onclick = 'showAlbum()'>Albums</a>";
                    echo "</div>";
                    echo "<br />";
                    echo "<div id = 'album's>";
                    echo "<table>";

                    $albumNode = $graphNode['albums'];
                    foreach($albumNode['data'] as $Node) {
                        if (count($Node['photos']['data']) == 0) {
                            echo "<tr><td>".$Node['name']."</td></tr>";
                            echo "<tr id = ".$Node['id']." style='display:none;'><td>";
                        } else {
                            echo "<tr><td><a href = '#' onclick = 'showPic(".$Node['id'].")'>".$Node['name']."</a></td></tr>";
                            echo "<tr id = ".$Node['id']." style='display:none;'><td>";

                            foreach($Node['photos']['data'] as $pic) {
                                $pic_query = "/".$pic['id']."/picture?redirect=false";
                                $response = $fb->get($pic_query);
                                $picNode = $response->getDecodedBody();
                                $HDurl=$picNode['data']['url'];
                                echo "<a href = ".$HDurl." target = 'view window'><img src = ".$pic['picture']." width = '80px' height = '80px'></a>";
                            }
                        }
                        echo "</td></tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                }
                
                echo "<br ／>";
            
                if(!isset($graphNode['posts'])) {
                    echo "<div class = 'notFound'>";
                    echo "No Posts has been found";
                    echo "</div>";
                } else {
                    echo "<div class = 'detail_h'>";
                    echo "<a href = '#' onclick = 'showPost()'>Posts</a>";
                    echo "</div>";
                    echo "<br />";
                    echo "<div id = 'post'>";
                    echo "<table>";
                    echo "<tr><th>Message</th></tr>";

                    $postNode = $graphNode['posts'];
                    foreach($postNode['data'] as $Node) {
                        if (isset($Node['message'])) {
                            echo "<tr><td>".$Node['message']."</td></tr>";
                        } else {
                            echo "<tr><td>Not found</td></tr>";
                        }
                    }
                    echo "</table>";
                    echo "</div>";
                }
            }
            
            ?>
        </div>       
    </body>
</html>