<?php
include_once __DIR__."/VSMModule/Preprocessing.php";
include_once __DIR__."/VSMModule/VSM.php";
include_once 'koneksi.php';

    // == STEP 1 inisialisasi
    $preprocess = new Preprocessing();
    $vsm = new VSM();

    // == STEP 2 mendapatkan kata dasar
    $query = $preprocess::preprocess($_POST['cari']);

    // == STEP 3 medapatkan dokumen ke array
    $connect = mysqli_query($koneksi, "SELECT * FROM tb_asli");
    $arrayDokumen = [];
    while ($row = mysqli_fetch_assoc($connect)) {
        $arrayDoc = [
            'id_doc' => $row['id'],
            'dokumen' => implode(" ", $preprocess::preprocess($row['isi']))
        ];
        array_push($arrayDokumen, $arrayDoc);
    }
    
    // STEP 4 == mendapatkan ranking dengan VSM
    $rank = $vsm::get_rank($query, $arrayDokumen);
 ?>
<html>

<head>
    <title>Sistem Temu Kembali Informasi</title>
    <link href="./css/global.min.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/dt/jq-3.3.1/dt-1.10.22/datatables.min.css" />
</head>

<body class="bg-white">

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
        <!-- main -->
        <div class="container mx-auto bg-white mt-5 p-10 rounded">
            <p class="py-5">Query yang dimasukkan : <?php echo $_POST['cari'] ?></p>
            <table id="example" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>
                            Rank
                        </th>
                        <th>
                            Judul
                        </th>
                        <th>
                            Deskripsi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rank as $row) {
        ?>
                    <tr>

                        <?php $id = $row["id_doc"];
                        $input = mysqli_query($koneksi,"SELECT * FROM tb_asli where id=$id");
                        $dok = mysqli_fetch_array($input);?>
                        <td><?php echo $row["ranking"]; ?></td>
                        <td><?php echo $dok["judul"]; ?> </td>
                        <td><?php echo $dok["isi"]; ?> </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>
    </div>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable({
            "order": [
                [0, "desc"]
            ],
            columnDefs: [{
                orderable: false,
                targets: 0
            }, {
                orderable: false,
                targets: 1
            }, {
                orderable: false,
                targets: 2
            }],
            "paging": false,
            "searching": false,
        });
    });
    </script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
</body>

</html>