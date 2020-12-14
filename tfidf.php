<html>

<?php

include "koneksi.php";
$result = mysqli_query($koneksi,"SELECT term FROM tb_term ORDER BY term"); // ambil data semua dokumen
while( $row = mysqli_fetch_assoc( $result ) ) {
 $query[] = $row[ 'term' ];
}
$totalQuery = count($query);


$dokumen = mysqli_query($koneksi,"SELECT * FROM tb_asli ORDER BY id"); // ambil data semua dokumen
$total_dokumen = mysqli_num_rows($dokumen);  //ambil jumlah dokumen
$df = array(); // buat nyimpen df
$tf = array(); // buat nyimpen tf
$a = array();  // buat nyimpen satuan df (sebelum di total menjadi df)
$idf = array(); // buat nyimpen idf
$w = array(); // buat nyimpen bobot
?>

<head>
    <title>Sistem Temu Kembali Informasi</title>
    <link href="./css/global.min.css" type="text/css" rel="stylesheet" />
</head>

<body class="bg-blue-300">

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <div class="min-h-screen">
        <div class="antialiased bg-gray-100 dark-mode:bg-gray-900">
            <div class="w-full text-gray-700 bg-white dark-mode:text-gray-200 dark-mode:bg-gray-800">
                <div x-data="{ open: true }"
                    class="flex flex-col max-w-screen-xl px-4 mx-auto md:items-center md:justify-between md:flex-row md:px-6 lg:px-8">
                    <div class="flex flex-row items-center justify-between p-4">
                        <a href="#"
                            class="text-lg font-semibold tracking-widest text-gray-900 uppercase rounded-lg dark-mode:text-white focus:outline-none focus:shadow-outline">STKI</a>
                        <button class="rounded-lg md:hidden focus:outline-none focus:shadow-outline"
                            @click="open = !open">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                <path x-show="!open" fill-rule="evenodd"
                                    d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z"
                                    clip-rule="evenodd"></path>
                                <path x-show="open" fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <nav :class="{'flex': open, 'hidden': !open}"
                        class="flex-col flex-grow hidden pb-4 md:pb-0 md:flex md:justify-end md:flex-row">
                        <a class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 md:mt-0 md:ml-4 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline"
                            href="/stki/index.php">Cari</a>
                        <a class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 md:mt-0 md:ml-4 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline"
                            href="/stki/upload.php">Upload</a>
                        <a class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 md:mt-0 md:ml-4 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline"
                            href="/stki/dok-list.php">List Dokumen</a>
                        <a class="px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:bg-transparent dark-mode:hover:bg-gray-600 dark-mode:focus:bg-gray-600 dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:text-gray-200 md:mt-0 md:ml-4 hover:text-gray-900 focus:text-gray-900 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline"
                            href="/stki/tfidf.php">TF-IDF</a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="container mx-auto bg-white mt-5 p-10 rounded">
            <p class="py-5">Menghitung TF-IDF di Semua Dokumen</b></p>
            <table class="border-collapse">
                <thead>
                    <tr>
                        <th
                            class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">
                            Query</th>
                        <?php for ($i = 1; $i <=$total_dokumen; $i++) { ?>
                        <th
                            class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">
                            <?php echo "D$i" ?></th>
                        <?php } ?>
                        <th
                            class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">
                            DF</th>
                        <th
                            class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">
                            D/DF</th>
                        <th
                            class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">
                            IDF</th>

                        <?php for ($i = 1; $i <=$total_dokumen; $i++) { ?>
                        <th
                            class="p-3 font-bold uppercase bg-gray-200 text-gray-600 border border-gray-300 hidden lg:table-cell">
                            <?php echo "WD$i" ?></th>
                        <?php } ?>

                    </tr>
                </thead>
                <tbody>
                    <?php
                      for ($x = 0; $x <= $totalQuery-1; $x++) {
                      $keyword = $query[$x]; ?>
                    <tr>
                        <td
                            class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b text-center block lg:table-cell relative lg:static">
                            <?php echo "$keyword"?></td>
                        <?php for ($i = 1; $i <=$total_dokumen; $i++) { 
                        $dokumenTerm = mysqli_query($koneksi,"SELECT * FROM tb_term WHERE term = '$keyword' AND urutan = $i");
                        $totalDokumenTerm = mysqli_num_rows($dokumenTerm); // mencari jumlah term tiap dokumen
                          if ($totalDokumenTerm != 0){ //
                          $a[$x][$i] = 1; //jika term di temukan maka satuan df 1 (sebelum ditotal menjadi df)
                        }else{
                          $a[$x][$i] = 0;
                        }//
                        $tf[$x][$i] = $totalDokumenTerm; //total term tiap dokumen di simpan disini
                        ?>
                        <td
                            class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b text-center block lg:table-cell relative lg:static">
                            <?php echo "$totalDokumenTerm" ?>
                        </td>
                        <?php } ?>
                        <?php 
                        $total = array_sum($a[$x]);
                        if ($total != 0){
                          $c = $total_dokumen/$total;
                          $idf[$x] = log10($c); // rumus menghitung idf dan menyimpannya di array
                        
                          }else {
                            $c = 0 ;
                            $idf[$x] = 0;
                          }
                        ?>
                        <td
                            class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b text-center block lg:table-cell relative lg:static">
                            <?php echo $total ?>
                        </td>
                        <td
                            class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b text-center block lg:table-cell relative lg:static">
                            <?php echo "$total_dokumen/$total" ?>
                        </td>
                        <td
                            class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b text-center block lg:table-cell relative lg:static">
                            <?php echo number_format((float)$idf[$x], 3, '.', ''); ?>
                        </td>
                        <?php
                        for ($i = 1; $i <=$total_dokumen; $i++) {
                        $term = $tf[$x][$i]; // mengambil tf yang di simpan di array tadi
                        $idef = $idf[$x]; // mengambil idf yang disimpan di array idf
                        $wa = $term * $idef; // rumus TF-IDF
                        $w[$x][$i] = $wa; // Menyimpan TF-IDF tiap dokumen di Array 
                        ?>
                        <td
                            class="w-full lg:w-auto p-3 text-gray-800 text-center border border-b text-center block lg:table-cell relative lg:static">
                            <?php echo number_format((float)$wa, 3, '.', ''); ?>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>