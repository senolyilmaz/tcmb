
Gunluk doviz kurlarini TCMB ( T.C. Merkez Bankasi ) uzerinden okuyup nesneye ceviren sinifin kullanimi asagida gosterilmistir.

<pre>
&lt;?php<br>
/*
USD ve EUR kurunu okumak icin asagiki kod ornegini kullanabilirsiniz.
*/

$tcmb = new <b>TCMB()</b>;
$kurlar = $tcbm->get_rates( ['USD','EUR'] );

?&gt;</pre><br><br>Sonuc Nesnesi : <br>

<pre>
stdClass Object
(
    [status] => 200
    [messages] => Array
        (
        )

    [data] => Array
        (
            [0] => stdClass Object
                (
                    [symbol] => USD
                    [buy] => 8.8407
                    [sell] => 8.8761
                )

            [1] => stdClass Object
                (
                    [symbol] => GBP
                    [buy] => 11.9067
                    [sell] => 11.9951
                )

        )

)
  </pre>
  <br><br>
  Not : Sonuc nesnesi icinde bulunan <b>messages</b> dizisi olusan hatalari icinde tutar. Bu hatalar <b>type</b> anahtariyla 1 : Kritik 2: Uyari gibi iki farkli sekilde olabilir.<br><br>
  <pre>
  10 - Sembol bulunamadi
  11 - XML kaynagindan veri alinamadi.
  12 - XML kaynagi diziye cevrilemedi
  13 - Dizi icinde Currency anahtari bulunamadi
  14 - Doviz kuru ( ??? ) okunmamadi
  </pre>
