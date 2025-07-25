<?php include("../../../config/connect.php"); ?>

<?php
session_start();
ob_clean(); // Cle // Return JSON response
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('display_errors', 0);
ini_set('error_log', 'error_log.txt');
$admin_id = $_SESSION['admin_id'];

if (!$admin_id) {
    echo "<script>alert('User not logged in.'); window.location.href='login_in2.php';</script>";
    exit;
} else {
    //echo "<script>alert('$user_id');</script>";
}




$sql = "SELECT p.user_id,u.name,u.user_name,u.user_type, u.profile_picture, p.picture_url,p.caption,p.upload_date,p.likes, p.picture_id FROM `pictures` p JOIN user u ON p.user_id=u.user_id 
           ORDER BY p.upload_date DESC";

$result2 = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <?php include('../../../library/library.php'); ?>
    <!-- For Latest Event Images can be deleted coursel-->
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
        rel="stylesheet" />

    <!-- For svgs sidebar-->
    <link
        href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
        rel="stylesheet" />

    <!-- For svgs sidebar-->
    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Post And Comment Using Alpine.js Don't Touch -->
    <script
        defer
        src="https://unpkg.com/alpinejs@3.1.1/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <title>Volunteer Management</title>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap");

        .transition-transform {
            transition-property: transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        @media (min-width: 768px) {
            .main.active {
                margin-left: 0px;
                width: 100%;
            }

            .footer.active {
                margin-left: 0px;
                width: 100%;
            }
        }

        @media (min-width: 768px) {
            .md\:ml-64 {
                margin-left: 16rem;
            }

            .md\:hidden {
                display: none;
            }

            .md\:w-\[calc\(100\%-256px\)\] {
                width: calc(100% - 256px);
            }

            .md\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .lg\:col-span-2 {
                grid-column: span 2 / span 2;
            }

            .lg\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .lg\:grid-cols-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        /* Style scrollbar track */
        ::-webkit-scrollbar {
            width: 5px;
        }

        /* Style scrollbar thumb */
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            /* display: none; */
        }

        /* Style scrollbar on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.7);
        }
    </style>
</head>

<body class="text-gray-800 font-inter">

    <!-- Sidebar -->
    <?php include('layouts/sidebar2.php'); ?>

    <!-- Main -->
    <main
        class="w-full md:w-[calc(100%-288px)] md:ml-72 bg-gray-200 min-h-screen transition-all main">

        <!-- Navbar -->
        <?php include('layouts/navbar2.php'); ?>

        <!-- Contents -->
        <div class="p-4 dynamiccontents" id="dynamiccontents">
            <div class="max-w-7xl mx-auto px-4 py-3">
                <div
                    class=" -mb-6 mx-auto space-y-2 text-center max-w-7xl bg-white py-4 rounded-xl">
                    <h2 class="text-4xl font-bold text-gray-800">Post Management</h2>
                    <p class="lg:mx-auto lg:w-6/12 text-gray-600 dark:text-gray-500">
                        Manage and monitor Posts and Comments
                    </p>
                </div>

                <div class="max-w-5xl mt-6 mx-auto space-y-3">

                    <?php

                    if ($result2->num_rows > 0) {

                        $rows = $result2->fetch_all(MYSQLI_ASSOC);

                        foreach ($rows as $row) {
                            $username = $row['name'];
                            $picture_creator_id = $row['user_id'];
                            $user_name = $row['user_name'];
                            $user_profile = $row['profile_picture'];
                            $picture_id = $row['picture_id'];
                            $picture_url = $row['picture_url'];
                            // $picture_url = preg_replace('/^\.\.\//', '', $picture_url);
                            $picture_caption = $row['caption'];
                            $picture_date = $row['upload_date'];
                            $picture_likes = $row['likes'];
                            // $user_profile = preg_replace('/^\.\.\//', '', $user_profile);
                            $comment_type = ($row['user_type'] == 'V') ? "Volunteer" : "Organisation";
                            $comment_style = ($row['user_type'] == 'V') ? "bg-indigo-100 text-indigo-800" : "bg-green-100 text-green-800";

                            $creation_date = new DateTime($picture_date);
                            $today = new DateTime();
                            $diff = $today->diff($creation_date);

                            if (
                                $diff->days == 0
                            ) {
                                $days_ago = "Today at: " . date("h:i A", strtotime($picture_date));
                            } else {
                                $days_ago = ($diff->days <= 10) ? "{$diff->days} days ago" : date('jS M y', strtotime($picture_date));
                            }

                            $sql = "SELECT * FROM `admin_manage_post` WHERE picture_id=? ORDER by date Desc limit 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $picture_id); // "i" for integer
                            $stmt->execute();
                            $result2 = $stmt->get_result();
                            $user_status = "Active";
                            $user_status_style = " bg-green-400 text-white ";
                            if ($result2->num_rows > 0) {
                                $row = $result2->fetch_assoc();
                                // echo $row['action'];
                                $user_status = ($row['action'] == "Suspend") ? "Deactive" : "Active";
                                $user_status_style = ($row['action'] == "Suspend") ? " bg-red-500 text-white " : " bg-green-400 text-white ";
                            }

                    ?>

                            <!-- Can be Commented  animation-->

                            <div
                                class="mx-auto flex justify-center items-center filter blur-2xl animate-pulse duration-500 transition w-full">
                                <div class="mt-2 mr-10 flex relative">
                                    <div
                                        class="p-44 rounded-full bg-gradient-to-r to-indigo-700 from-pink-900 absolute top-20 right-0"></div>
                                    <div
                                        class="p-44 rounded-full bg-gradient-to-r to-pink-700 from-indigo-900 absolute md:flex hidden"></div>
                                </div>
                                <!-- Right Side -->
                                <div class="flex flex-col absolute top-8 right-10 space-y-4">
                                    <div
                                        class="p-5 rounded-full bg-gradient-to-r to-pink-700 via-red-500 from-indigo-900 absolute right-16 top-10"></div>
                                </div>
                                <div class="flex flex-col absolute bottom-8 right-10 space-y-4">
                                    <div
                                        class="p-10 rounded-full bg-gradient-to-r to-pink-700 from-indigo-900 absolute right-16 bottom-10"></div>
                                </div>
                                <!--  Left side -->
                                <div
                                    class="flex flex-col space-y-2 filter animate-pulse duration-500">
                                    <div
                                        class="p-10 bg-gradient-to-r to-indigo-700 from-blue-900 absolute top-20 left-20"></div>
                                    <div
                                        class="p-10 bg-gradient-to-r to-indigo-700 from-blue-900 absolute bottom-20 right-20"></div>
                                </div>
                            </div>
                            <!-- Can be Commented  end-->






                            <!-- 1st Post  -->
                            <div
                                class="mx-auto flex justify-center max-w-4xl bg-white rounded-lg items-center relative md:p-0 p-8"
                                x-data="{
        comment : false,
    }">
                                <div class="h-full relative">
                                    <div class="py-2 px-0">
                                        <div class="flex justify-between items-center py-2">
                                            <div class="relative mt-1 flex">

                                                <div class="mr-2 p-1">
                                                    <img
                                                        src="<?= $user_profile ?>"
                                                        alt="<?= $username ?>"
                                                        class="w-10 h-10 rounded-full object-cover" />
                                                </div>
                                                <a onclick="window.location.href='profile2.php?id=<?= base64_encode($picture_creator_id) ?>'">
                                                    <div class="ml-3 flex justify-start flex-col items-start">
                                                        <p class="text-lg font-bold "><?= $username ?> <span class=" ml-2  text-sm <?= $comment_style ?> rounded-xl px-2 py-1"><?= $comment_type ?></span></p>
                                                        <p class="text-gray-600 text-sm font-mono">@<?= $user_name ?></p>
                                                    </div>
                                                </a>
                                                <!-- <span class="text-xs mx-2">•</span>
                       <button class="text-indigo-500 text-sm capitalize flex justify-start items-start">follow</button> -->

                                            </div>
                                            <!-- <button
                                                type="button"
                                                class="relative p-2 focus:outline-none border-none bg-gray-100 rounded-full">
                                                <svg
                                                    class="w-5 h-5 text-gray-700"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                </svg>
                                            </button> -->
                                            <div class="flex space-x-2 mr-3 post-action">
                                                <!-- <button class="p-2 text-gray-400 hover:text-blue-600" onclick="window.location.href='edit_post.php?id=<?= base64_encode($picture_id) ?>'">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button class="delete-post p-2 text-gray-400 hover:text-red-500" data-postid="<?= $picture_id ?>">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button> -->
                                                <?php

                                                if ($user_status == "Active") {


                                                ?>
                                                    <button disabled
                                                        class=" bg-emerald-500 opacity-30 text-gray-200 px-6 py-2 rounded-lg text-sm  font-semibold duration-300 transition-all">
                                                        Active
                                                    </button>
                                                    <button data-postid="<?= $picture_id ?>" data-action="Suspend"
                                                        class=" bg-red-500 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:scale-105 duration-300 transition-all">
                                                        Deactive
                                                    </button>

                                                <?php
                                                } else {


                                                ?>
                                                    <button data-postid="<?= $picture_id ?>" data-action="unuspend"
                                                        class=" bg-emerald-500  text-white px-6 py-2 rounded-lg text-sm  font-semibold hover:scale-105 duration-300 transition-all">
                                                        Active
                                                    </button>
                                                    <button disabled
                                                        class=" opacity-30 bg-red-500 text-gray-200 px-6 py-2 rounded-lg text-sm font-semibold hover:scale-105 duration-300 transition-all">
                                                        Deactive
                                                    </button>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative w-full h-full">
                                        <img
                                            src="<?= $picture_url ?>"
                                            alt="<?= $username ?>"
                                            class="rounded-lg w-full h-full object-cover" />
                                    </div>
                                    <div class="">
                                        <!-- Comment -->
                                        <div
                                            class="overflow-y-scroll w-full absolute inset-0 bg-white transform transition duration-200"
                                            x-show="comment"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 transform scale-90"
                                            x-transition:enter-end="opacity-100 transform scale-100"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100 transform scale-100"
                                            x-transition:leave-end="opacity-0 transform scale-90">
                                            <div
                                                class="flex justify-start items-center py-2 px-4 border-b"
                                                @click="comment = !comment">
                                                <svg
                                                    class="w-8 h-8 text-gray-700"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                                                </svg>
                                                <div
                                                    class="text-xl w-full text-center p-4 font-semibold justify-between">
                                                    Comments
                                                </div>
                                            </div>

                                            <div class="p-2 mb-10" id="comment_list_<?= $picture_id ?>">

                                                <?php
                                                // Calculate the average rating of the event
                                                $sql = "SELECT COUNT(*) AS Total FROM `comments` WHERE picture_id=?";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->bind_param("i", $picture_id);
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $row = $result->fetch_assoc();
                                                $comments_total = $row['Total'];




                                                // // Fetch reviews from the database based on date_time order
                                                $sql = "SELECT c.text,c.date_time,c.comment_id,u.user_id, u.name, u.profile_picture,
                                                            u.user_name,u.user_type FROM comments c JOIN user u ON c.user_id = u.user_id WHERE c.picture_id = ? ORDER BY `date_time` DESC";

                                                $stmt = $conn->prepare($sql);
                                                $stmt->bind_param("i", $picture_id);
                                                $stmt->execute();
                                                $result = $stmt->get_result();

                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        $comment_id = $row['comment_id'];
                                                        $comment_text = $row['text'];
                                                        $comment_date = $row['date_time'];
                                                        $comment_user_id = $row['user_id'];
                                                        $comment_name = $row['name'];
                                                        $comment_user_name = $row['user_name'];
                                                        $comment_user_profile = $row['profile_picture'];
                                                        // $comment_user_profile = preg_replace('/^\.\.\//', '', $comment_user_profile);
                                                        $comment_user_style = ($row['user_type'] == 'O') ? "bg-indigo-100 " : "";
                                                        $comment_creation_date = new DateTime($comment_date);
                                                        $comment_today = new DateTime();
                                                        $comment_diff = $comment_today->diff($comment_creation_date);
                                                        if (
                                                            $comment_diff->days == 0
                                                        ) {
                                                            $comment_days_ago = "Today at: " . date("h:i A", strtotime($comment_date));
                                                        } else {
                                                            $comment_days_ago = ($comment_diff->days <= 10) ? "{$comment_diff->days} days ago" : date('jS M y', strtotime($comment_date));
                                                        }


                                                ?>


                                                        <!-- 1nd Comment -->
                                                        <div
                                                            class="flex justify-start <?= $comment_user_style ?> flex-col space-y-3 items-start px-2 border-b border-gray-300 rounded-sm">
                                                            <div class="relative w-full mt-1 mb-3 pt-2 flex">
                                                                <div class="mr-2">
                                                                    <img
                                                                        src="<?= $comment_user_profile ?>"
                                                                        alt="<?= $comment_name ?>"

                                                                        class="w-12 h-12 rounded-full object-cover" />
                                                                </div>
                                                                <div class="ml-2 w-full">
                                                                    <p class="text-gray-600 md:text-lg text-xs w-full">
                                                                        <!-- Username User -->
                                                                        <span class=" text-gray-900 mr-2 font-mono">@<?= $comment_user_name ?></span>
                                                                        <!-- Username Comment -->
                                                                        <?= $comment_text ?>
                                                                    </p>
                                                                    <div class="time mt-1 text-gray-400 text-xs">
                                                                        <p><?= $comment_days_ago ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                <?php

                                                    }
                                                } else {
                                                    echo "<h1 class='text-xl mt-10 text-center font-serif font-medium'>No Comments Available</h1>";
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <!-- System Like and tools Feed -->
                                        <div class="flex justify-between items-start p-2 py-">
                                            <div class="flex space-x-2 items-center">
                                                <button type="button" class=" heart2 focus:outline-none Like" data-pictureid=<?= $picture_id ?>>
                                                    <svg
                                                        class="w-8 h-8 heart hover:fill-red-500 hover:text-red-500 text-gray-600"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="1.6"
                                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                    </svg>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="focus:outline-none Comment"
                                                    @click="comment = !comment">
                                                    <svg
                                                        class="w-8 h-8 text-gray-600"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="1.6"
                                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" data-pictureid=<?= base64_encode($picture_id) ?> class=" whatsappShare focus:outline-none save">
                                                    <svg
                                                        class="w-7 h-7 mb-1 ml-1 text-gray-600 z-10"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="1.6"
                                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="flex space-x-2 items-center">
                                                <button type="button" class="focus:outline-none Like">
                                                    <svg
                                                        class="w-8 h-8 text-gray-600"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="1.6"
                                                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Post Details -->
                                        <div class="p-2 ml-2 mr-2 flex flex-col space-y-3 mb-4">
                                            <div class="w-full">
                                                <p class="font-bold text-lg text-gray-700" id="likes_<?= $picture_id ?>"><?= $picture_likes ?> likes</p>
                                            </div>
                                            <div class="text-base">
                                                <?= $picture_caption ?>
                                            </div>

                                            <div id="comment_count_<?= $picture_id ?>" class="text-gray-500 leading-loose text-base font-semibold">
                                                View all <?= $comments_total ?> comments
                                            </div>

                                            <div class="w-full">
                                                <p class="text-base font-medium text-gray-400"><?= $days_ago ?></p>
                                            </div>
                                        </div>

                                        <!-- Comment Input Field ans send button -->
                                        <!-- End System Like and tools Feed  -->

                                    </div>
                                </div>
                            </div>
                            <!-- End 1st Post -->

                    <?php
                        }
                    } else {
                        echo '
                    <h1 class=" text-3xl font-medium text-center mt-10">No Post Available</h1>
                    ';
                    }
                    ?>

                </div>
            </div>
        </div>
        <!-- End Content -->

    </main>

    <script>
        $(document).ready(function() {

            $(".whatsappShare").click(function() {
                let imgUrl = window.location.href; // Gets the current event page URL
                let picture_Id = $(this).data("pictureid"); // Replace with dynamic PHP variable
                // alert(picture_Id);
                // let whatsappUrl = `https://api.whatsapp.com/send?text=Check%20out%20this%20event!%20${encodeURIComponent(imgUrl)}`;
                // window.open(whatsappUrl, "_blank");

                let baseUrl = window.location.origin + "/volunteer-management-system/main/pages/view_post.php?id=" + picture_Id; // Encode ID in base64
                let whatsappUrl = `https://api.whatsapp.com/send?text=Check%20out%20This%20AmazingPost!%20${encodeURIComponent(baseUrl)}`;

                window.open(whatsappUrl, "_blank");


            });

            $(".heart").click(function() {
                //  alert('hello');
                $(".heart").removeClass("selected fill-red-500 text-red-500").addClass("text-gray-600");
                $(this).prevAll().addBack().addClass(" selected  fill-red-500 text-red-500").removeClass("text-gray-600");

            });


            $(".heart2").click(function() {
                //  alert('hello');
                let post = $(this).data("pictureid");
                //alert(post);
                let comment_likes = ("#likes_" + post);

                $.ajax({
                    url: "../Backend/add_likes_comment.php", // Backend script to handle the comment
                    type: "POST",
                    data: {
                        post_id: post
                    },
                    dataType: "json", // Expect JSON response
                    success: function(response) {
                        if (response.status === 'success') {
                            // alert(response.message); // Show success message
                            // form.find(".comment-input").val(""); // Clear input field


                            $(comment_likes).html(`${response.like} likes`);
                            //location.reload(); // Reload the page after deletion
                        } else {
                            alert(response.message); // Show error message
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error: " + status + " " + error);
                        console.log("Server Response: " + xhr.responseText);
                    }
                });

            });


            $(document).on('click', '.post-action button', function() {
                let action = $(this).data("action");
                let postid = $(this).data("postid");

                // alert(action);
                // alert(postid);
               
                    $.ajax({
                        url: "backend/post_management.php", // Backend PHP script
                        method: "POST",
                        data: {
                            action: action,
                            post_id: postid
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                alert(response.message); // Show success message
                                location.reload();
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("AJAX Error: " + status + " " + error);
                            alert("AJAX Error: " + status + " " + error);
                        }
                    });

                
            });



        });
    </script>




    <!-- Footer -->
    <?php include('layouts/footer2.php'); ?>

    <!-- Home Page NavBar Sidebar Don't Touch -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../../js/main.js"></script>

    <!-- Home Page NavBar Sidebar Don't Touch -->
    <script>
        // start: Sidebar
        const sidebarToggle = document.querySelector(".sidebar-toggle");
        const sidebarOverlay = document.querySelector(".sidebar-overlay");
        const sidebarMenu = document.querySelector(".sidebar-menu");
        const footer = document.querySelector(".footer");
        const main = document.querySelector(".main");

        sidebarToggle.addEventListener("click", function(e) {
            e.preventDefault();
            main.classList.toggle("active");
            footer.classList.toggle("active");
            sidebarOverlay.classList.toggle("hidden");
            sidebarMenu.classList.toggle("-translate-x-full");
        });
        sidebarOverlay.addEventListener("click", function(e) {
            e.preventDefault();
            main.classList.add("active");
            footer.classList.add("active");
            sidebarOverlay.classList.add("hidden");
            sidebarMenu.classList.add("-translate-x-full");
        });
        document
            .querySelectorAll(".sidebar-dropdown-toggle")
            .forEach(function(item) {
                item.addEventListener("click", function(e) {
                    e.preventDefault();
                    const parent = item.closest(".group");
                    if (parent.classList.contains("selected")) {
                        parent.classList.remove("selected");
                    } else {
                        document
                            .querySelectorAll(".sidebar-dropdown-toggle")
                            .forEach(function(i) {
                                i.closest(".group").classList.remove("selected");
                            });
                        parent.classList.add("selected");
                    }
                });
            });
        // end: Sidebar

        // start: Popper
        const popperInstance = {};
        document.querySelectorAll(".dropdown").forEach(function(item, index) {
            const popperId = "popper-" + index;
            const toggle = item.querySelector(".dropdown-toggle");
            const menu = item.querySelector(".dropdown-menu");
            menu.dataset.popperId = popperId;
            popperInstance[popperId] = Popper.createPopper(toggle, menu, {
                modifiers: [{
                        name: "offset",
                        options: {
                            offset: [0, 8],
                        },
                    },
                    {
                        name: "preventOverflow",
                        options: {
                            padding: 24,
                        },
                    },
                ],
                placement: "bottom-end",
            });
        });
        document.addEventListener("click", function(e) {
            const toggle = e.target.closest(".dropdown-toggle");
            const menu = e.target.closest(".dropdown-menu");
            if (toggle) {
                const menuEl = toggle
                    .closest(".dropdown")
                    .querySelector(".dropdown-menu");
                const popperId = menuEl.dataset.popperId;
                if (menuEl.classList.contains("hidden")) {
                    hideDropdown();
                    menuEl.classList.remove("hidden");
                    showPopper(popperId);
                } else {
                    menuEl.classList.add("hidden");
                    hidePopper(popperId);
                }
            } else if (!menu) {
                hideDropdown();
            }
        });

        function hideDropdown() {
            document.querySelectorAll(".dropdown-menu").forEach(function(item) {
                item.classList.add("hidden");
            });
        }

        function showPopper(popperId) {
            popperInstance[popperId].setOptions(function(options) {
                return {
                    ...options,
                    modifiers: [
                        ...options.modifiers,
                        {
                            name: "eventListeners",
                            enabled: true
                        },
                    ],
                };
            });
            popperInstance[popperId].update();
        }

        function hidePopper(popperId) {
            popperInstance[popperId].setOptions(function(options) {
                return {
                    ...options,
                    modifiers: [
                        ...options.modifiers,
                        {
                            name: "eventListeners",
                            enabled: false
                        },
                    ],
                };
            });
        }
        // end: Popper

        // start: Tab
        document.querySelectorAll("[data-tab]").forEach(function(item) {
            item.addEventListener("click", function(e) {
                e.preventDefault();
                const tab = item.dataset.tab;
                const page = item.dataset.tabPage;
                const target = document.querySelector(
                    '[data-tab-for="' + tab + '"][data-page="' + page + '"]'
                );
                document
                    .querySelectorAll('[data-tab="' + tab + '"]')
                    .forEach(function(i) {
                        i.classList.remove("active");
                    });
                document
                    .querySelectorAll('[data-tab-for="' + tab + '"]')
                    .forEach(function(i) {
                        i.classList.add("hidden");
                    });
                item.classList.add("active");
                target.classList.remove("hidden");
            });
        });
    </script>
</body>

</html>