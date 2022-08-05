<?php
error_reporting(0);

$encoded = $_POST['code'];
$version = '4.0';
if (isset($encoded)) {
    if (strpos($encoded, $version) !== false) {

        $rand = rand(10000, 100000);
        file_put_contents($rand . '.txt', $encoded);
        $name =  $rand . '.txt';

        function updateFile()
        {
            global $encoded_file;
            global $file;
            global $name;
            $encoded_file = $name;
            $file = file_get_contents($encoded_file);
            return $file;
        }
        function cleanFile()
        {
            updateFile();
            global $encoded_file;
            global $file;
            file_put_contents($encoded_file, strstr($file, '.$_'));
        }
        function extractBase64()
        {
            updateFile();
            global $encoded_file;
            global $file;
            if (preg_match('/"([^"]+)"/', $file, $m)) {
                file_put_contents($encoded_file, $m[1]);
            }
        }
        function decodeBase64()
        {
            updateFile();
            global $encoded_file;
            global $file;
            file_put_contents($encoded_file, base64_decode($file));
        }

        function delete_all_between($beginning, $end, $string)
        {
            global $encoded_file;
            $beginningPos = strpos($string, $beginning);
            $endPos = strpos($string, $end);
            if ($beginningPos === false || $endPos === false) {
                return $string;
            }
            $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
            $result = delete_all_between($beginning, $end, str_replace($textToDelete, '', $string));
            file_put_contents($encoded_file, $result);
        }

        function CED()
        {
            // Clean -> Extract -> Decode (BASE64)
            cleanFile();
            extractBase64();
            decodeBase64();
        }
        function ED()
        {
            // Extract -> Decode (BASE64)
            extractBase64();
            decodeBase64();
        }
        function step1()
        {
            CED();
            CED();
            CED();
        }
        function step2()
        {
            global $file;
            updateFile();
            delete_all_between('p $_', '@eval("?>".', $file);
            updateFile();
            delete_all_between('?ph', '.$_', $file);
            ED();
            updateFile();
            delete_all_between('function', 'eval', $file);
            ED();
            updateFile();
            delete_all_between('$_', '*/', $file);
            extractBase64();
            updateFile();
        }
        function step3()
        {
            global $file, $name, $encoded_file;
            $decoded_file = $name . '_decoded.txt';
            $result = file_put_contents($decoded_file, (gzinflate(base64_decode($file))));
            $dl = $decoded_file;
            header('Content-Disposition: attachment; filename="Decoded.php"');
            readfile($dl);
            unlink($encoded_file);
            unlink($decoded_file);
        }

        // Functions Finishes here 

        step1();
        step2();
        step3();
    } else {
        header('location:invalid.html');
    }
} else {
    header('location:index.html');
}
