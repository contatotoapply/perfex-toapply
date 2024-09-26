<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/css/chat.css'); ?>">
<?php init_head(); ?>
<?php
$csrfToken = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;

?>

<div id="wrapper">
    <div id="app">
        <div class="min-h-screen grid place-items-center">
            <div class="w-full p-1 addstl">
                <div class="bg-green-200 border border-gray-600 text-green-800 p-4 rounded-md flex justify-between items-center">
                    <p class="text-sm"><?php echo _l('chat_message_note'); ?></p>
                    <button class="hideMessage text-green-600 hover:text-green-800 focus:outline-none">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div v-if="errorMessage" class="bg-red-200 border border-red-600 text-red-800 p-4 rounded-md  w-full flex justify-between items-center">
                <p class="text-sm">{{ errorMessage }}</p>
                <button class="hideMessage text-red-600 hover:text-red-800 focus:outline-none">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div v-if="sameNumErrorMessage" class="bg-red-200 border border-red-600 text-red-800 w-full p-4 rounded-md flex justify-between items-center">
                <p class="text-sm">{{ sameNumErrorMessage }}</p>
                <button class="hideMessage text-red-600 hover:text-red-800 focus:outline-none">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="w-[100%] h-[100%] bg-gray-200 flex flex-row">
                <!-- Sidebar Start -->
                <div class="left bg-[#ffffff] h-full w-[0%] md:w-[25%]">
                    <div class="sticky flex flex-col">
                        <div class="bg-[#F0F2F5] flex justify-start gap-4 items-center w-full px-4 py-[0.60rem] border-r border-slate-300">
                            <div class="dp flex justify-center items-center w-[40px] h-[40px]">
                                <img class="rounded-full" src="<?= !empty(get_option("wac_profile_picture_url")) ? get_option("wac_profile_picture_url") : base_url('assets/images/user-placeholder.jpg') ?>" alt="profile">
                            </div>
                            <div class="tools flex justify-center items-center space-x-2" v-if="wb_selectedinteraction && typeof wb_selectedinteraction === 'object'">
                                <p><?php echo _l('from'); ?> {{ wb_selectedinteraction.wa_no }}</p>
                            </div>
                        </div>

                        <div class="px-4 py-2 flex items-center">
                            <div class="flex justify-between items-center w-full">
                                <select v-model="wb_selectedWaNo" v-on:change="wb_filterInteractions" id="wb_selectedWaNo" class="w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset focus:ring-inset bg-[#F0F2F5] focus:ring-blue-500 sm:text-sm sm:leading-6  mr-2">
                                    <option v-for="(interaction, index) in wb_uniqueWaNos" :key="index" :value="interaction.wa_no" :selected="wb_selectedWaNo === 'interaction.wa_no'">
                                        {{ interaction.wa_no }}
                                    </option>
                                    <option value="*"><?php echo _l('all_chat'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="px-4 py-2 flex items-center">
                            <div class="flex justify-between items-center w-full">
                                <i class="fa fa-search absolute left-10"></i>
                                <input id="wb_searchText" class="outline-none bg-[#F0F2F5] rounded-md pl-16 py-1 w-full mr-2"
                                    placeholder="Search"
                                    v-model="wb_searchText"
                                    type="text" name="wb_searchText">

                            </div>
                        </div>

                        <hr class="h-[0.01px] bg-slate-100">
                        <div class="h-[100vh] overflow-y-auto">
                            <div class="chatbox hover:bg-gray-100 cursor-pointer" v-for="(interaction, index) in wb_displayedInteractions" :key="interaction.id" v-on:click="wb_selectinteraction(interaction.id)" :class="{'bg-gray-200': wb_selectedinteraction && wb_selectedinteraction.id === interaction.id}">
                                <hr class="w-[100%] float-right">
                                <div class="flex items-center gap-2 w-full">
                                    <div class="p-3 flex items-center justify-center">
                                        <p class="rounded-full bg-green-400 w-12 h-12 flex items-center justify-center text-center font-semibold text-gray-700">
                                            {{ wb_getAvatarInitials(interaction.name) }}
                                        </p>
                                    </div>
                                    <div class="side-chat flex justify-between w-[75%] ">
                                        <div class="chat-name flex flex-col">
                                            <div class="flex justify-1 items-center gap-2">
                                                <h3 class="text-md text-gray-700 font-sans font-semibold">{{ interaction.name }}</h3>
                                                <p class="text-md text-gray-500 font-sans font-normal flex items-center mb-1">
                                                    <span
                                                        :class="{
                                                        'bg-violet-100 text-purple-800': interaction.type === 'leads',
                                                        'bg-red-100 text-red-800': interaction.type === 'contacts',
                                                    }"
                                                        class="inline-block mt-1 text-xs font-semibold px-2 rounded ">
                                                        {{ interaction.type }}
                                                    </span>
                                                </p>
                                            </div>
                                            <span v-html="wb_truncateText(interaction.last_message, 2)"></span>
                                        </div>


                                        <div class="flex flex-col gap-2 items-end">
                                            <p class="text-[0.80rem] font-sans text-gray-500 font-normal">{{ wb_formatTime(interaction.time_sent)
											}}</p>
                                            <div class="flex gap-4 justify-center items-center">
                                                <span v-on:click="wb_deleteInteraction(interaction.id)" class="hide dele-icn"><i class="fa-solid text-red-500 float-right fa-trash" data-toggle="tooltip" data-placement="top" title="<?php echo _l('remove_chat'); ?>"></i>
                                                </span>
                                                <span v-if="wb_countUnreadMessages(interaction.id) > 0" class="bg-green-500 text-white text-xs font-semibold py-1 px-2 rounded-full">
                                                    {{
                                                    wb_countUnreadMessages(interaction.id) }}
                                                </span>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <hr class="w-[100%] float-right">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sidebar End -->

                <!-- Main content start-->
                <div class="right bg-[url('<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/images/bg.png'); ?>')] h-full w-[100%] md:w-[75%] " v-if="wb_selectedinteraction && typeof wb_selectedinteraction === 'object'">
                    <div class="bg-[#F0F2F5] flex justify-between items-center px-4 py-[0.40rem]">
                        <div class="flex justify-between items-center w-full cursor-pointer">
                            <div class="flex justify-between items-center space-x-4">
                                <p class="rounded-full w-12 h-12 flex items-center justify-center text-center font-semibold text-gray-700 bg-green-400">
                                    {{wb_getAvatarInitials(wb_selectedinteraction.name) }}
                                </p>
                                <div class="flex flex-col">
                                    <p class="text-slate-600 font-sans text-nowrap font-medium text-base">{{ wb_selectedinteraction.name }}</p>

                                    <p class="text-slate-400 flex items-center justify-start gap-2 font-sans font-medium text-[0.75rem]"><i class="fa fa-phone"></i> +{{
										wb_selectedinteraction.receiver_id }}</p>

                                </div>
                            </div>
                            <?php if (is_admin()) { ?>
                                <div class="flex gap-3 items-center justify-center md:w-1/2">
                                    <div v-if="wb_selectedinteraction.agent_name.agent_name" class="flex items-center">
                                        <span class="mr-1">
                                            <i class="fa-lg fa-regular fa-user mr-2" data-tooltip="true" data-placement="top" title="<?php echo _l('support_agent'); ?>"></i>
                                        </span>
                                        <div class="inline-flex items-center space-x-2">
                                            <div class="flex -space-x-1" v-html="wb_selectedinteraction.agent_icon"></div>
                                        </div>
                                    </div>
                                    <button type="button" class="bg-gray-500 text-white text-sm px-3 py-4 rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500" v-on:click="wb_initAgent">
                                        <i class="fa-solid fa-user-pen fa-lg" data-tooltip="true" data-placement="top" title="<?php echo _l('change_support_agent'); ?>"></i>
                                    </button>
                                </div>
                            <?php } ?>
                            <div class="flex justify-end items-center w-4/12">
                                <span v-if="wb_selectedinteraction.last_msg_time" v-html="wb_alertTime(wb_selectedinteraction.last_msg_time)"></span>
                            </div>
                        </div>
                    </div>

                    <?php if (is_admin()) { ?>
                        <div class="modal fade" id="AgentModal" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo _l('modal_title'); ?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <?= render_select(
                                            'assigned[]',
                                            $members,
                                            ['staffid', ['firstname', 'lastname']],
                                            '',
                                            '',
                                            ['data-width' => '100%', 'multiple' => true, 'data-actions-box' => true],
                                            [],
                                            '',
                                            '',
                                            false
                                        ); ?>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _l('close_btn'); ?></button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="wb_handleAssignedChange"><?php echo _l('save_btn'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="relative flex justify-center ">
                        <div class="absolute top-[5%] w-6/12 addstl">
                            <div v-if="overdueAlert" v-html="overdueAlert" class="mt-4"></div>
                        </div>
                    </div>
                    <!-- chat-section Start -->
                    <div class="max-h-[100vh] p-4 overflow-y-auto" ref="wb_chatContainer">
                        <div v-if="wb_selectedinteraction && wb_selectedinteraction.messages">
                            <div v-for="(message, index) in wb_selectedinteraction.messages" :key="index">
                                <!-- Message from the left -->
                                <div class="flex justify-center" v-if="wb_shouldShowDate(message, wb_selectedinteraction.messages[index - 1])">
                                    <span class="bg-white py-1 px-2 text-xs rounded-md">
                                        {{getDate(message.time_sent) }}
                                    </span>
                                </div>
                                <div :class="['flex', message.sender_id === wb_selectedinteraction.wa_no ? 'justify-end mb-8 ' : 'justify-start mb-4']">
                                    <div :class="[
												'bg-white border border-gray-300 p-2 break-words rounded-lg max-w-xs',
												message.sender_id === wb_selectedinteraction.wa_no ? 'bg-[#82ee8aa6]' : 'bg-white',
												message.staff_id == 0 && message.sender_id === wb_selectedinteraction.wa_no ? 'bg-[#e7e7e7]' : '',
												message.type === 'text' && message.message.length > 50 ? 'max-w-xs' : ''
											]" v-bind="message.sender_id === wb_selectedinteraction.wa_no ? {
												'data-tooltip': message.staff_name,
												'data-placement': 'left',
												'title': message.staff_name
											} : {}">
                                        <template v-if="message.ref_message_id">
                                            <div class="bg-neutral-100 rounded-lg mb-2">
                                                <div class="flex flex-col gap-2 p-2">
                                                    <p class="text-gray-400 font-normal"><?php echo _l('replying_to'); ?></p>
                                                    <p class="text-gray-800" v-html="getOriginalMessage(message.ref_message_id).message"></p>
                                                    <div v-if="getOriginalMessage(message.ref_message_id).assets_url">
                                                        <template v-if="getOriginalMessage(message.ref_message_id).type === 'image'">
                                                            <img :src="getOriginalMessage(message.ref_message_id).asset_url" class="rounded-lg max-w-xs max-h-28" alt="Image">
                                                        </template>
                                                        <template v-if="getOriginalMessage(message.ref_message_id).type === 'video'">
                                                            <video :src="getOriginalMessage(message.ref_message_id).asset_url" controls class="rounded-lg max-w-xs max-h-28"></video>
                                                        </template>
                                                        <template v-if="getOriginalMessage(message.ref_message_id).type === 'document'">
                                                            <a :href="getOriginalMessage(message.ref_message_id).asset_url" target="_blank" class="text-blue-500 underline"><?php echo _l('download_document'); ?></a>
                                                        </template>
                                                        <template v-if="getOriginalMessage(message.ref_message_id).type === 'audio'">
                                                            <audio controls class="w-[250px]">
                                                                <source :src="getOriginalMessage(message.ref_message_id).asset_url" type="audio/mpeg">
                                                            </audio>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <!-- Conditional rendering for different message types -->
                                        <template v-if="message.type === 'interactive'">
                                            <p class="text-gray-800 text-sm">{{ message.message }}</p>
                                        </template>

                                        <template v-if="message.type === 'text'">
                                            <p class="text-gray-800 text-sm" v-html="message.message"></p>
                                        </template>

                                        <template v-if="message.type === 'button'">
                                            <p class="text-gray-800 text-sm" v-html="message.message"></p>
                                        </template>

                                        <template v-if="message.type === 'reaction'">
                                            <p class="text-gray-800 text-sm" v-html="message.message"></p>
                                        </template>

                                        <template v-else-if="message.type === 'image'">
                                            <a :href="message.asset_url" data-lightbox="image-group" target="_blank">
                                                <img :src="message.asset_url" alt="Image" class="rounded-lg max-w-xs max-h-28">
                                            </a>
                                            <p class="text-gray-600 text-xs mt-2" v-if="message.caption">{{ message.caption }}</p>
                                        </template>

                                        <template v-else-if="message.type === 'video'">
                                            <video :src="message.asset_url" controls class="rounded-lg max-w-xs max-h-28"></video>
                                            <p class="text-gray-600 text-xs mt-2" v-if="message.message">{{ message.message }}</p>
                                        </template>

                                        <template v-else-if="message.type === 'document'">
                                            <a :href="message.asset_url" target="_blank" class="text-blue-500 underline"><?php echo _l('download_document'); ?></a>
                                        </template>

                                        <template v-else-if="message.type === 'audio'">
                                            <audio controls class="w-[300px]">
                                                <source :src="message.asset_url" type="audio/mpeg">
                                            </audio>
                                            <p class="text-gray-600 text-xs mt-2" v-if="message.message">{{ message.message }}</p>
                                        </template>

                                        <!-- Message Timestamp and Status -->
                                        <div class="flex justify-between items-center gap-4 mt-2 text-xs text-gray-600">
                                            <span>{{ wb_getTime(message.time_sent) }}</span>
                                            <div>
                                                <span v-on:click="replyToMessage(message)" class="cursor-pointer">
                                                    <i class="fa-solid fa-reply"></i>
                                                </span>
                                                <span v-if="message.sender_id === wb_selectedinteraction.wa_no" class="ml-2">
                                                    <i v-if="message.status === 'sent'" class="fa fa-check text-gray-500" title="Sent"></i>
                                                    <i v-else-if="message.status === 'delivered'" class="fa fa-check-double text-gray-500" title="Delivered"></i>
                                                    <i v-else-if="message.status === 'read'" class="fa fa-check-double text-cyan-500" title="Read"></i>
                                                    <i v-else-if="message.status === 'failed'" class="fa fa-exclamation-circle text-red-500" title="Failed"></i>
                                                    <i v-else-if="message.status === 'deleted'" class="fa fa-trash text-red-500" title="Deleted"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat-section End -->

                    <!-- reply reaction messages section start -->
                    <div v-if="replyingToMessage" class="relative flex justify-center">
                        <div class="absolute bottom-[21px] w-11/12 bg-[#F0F2F5] rounded-lg">
                            <div class="bg-white w-full max-w-full p-4 rounded-lg shadow-lg flex justify-between items-center">
                                <div class="flex flex-col gap-2">
                                    <p class="text-gray-400 font-normal"><?php echo _l('replying_to'); ?></p>
                                    <p class="text-gray-800 font-semibold" v-html="replyingToMessage.message"></p>
                                    <div v-if="replyingToMessage.asset_url">
                                        <template v-if="replyingToMessage.type === 'image'">
                                            <img :src="replyingToMessage.asset_url" class="rounded-lg max-w-xs max-h-28" alt="Image">
                                        </template>
                                        <template v-if="replyingToMessage.type === 'video'">
                                            <video :src="replyingToMessage.asset_url" controls class="rounded-lg max-w-xs max-h-28"></video>
                                        </template>
                                        <template v-if="replyingToMessage.type === 'document'">
                                            <a :href="replyingToMessage.asset_url" target="_blank" class="text-blue-500 underline"><?php echo _l('download_document'); ?></a>
                                        </template>
                                        <template v-if="replyingToMessage.type === 'audio'">
                                            <audio controls class="w-[250px]">
                                                <source :src="replyingToMessage.asset_url" type="audio/mpeg">
                                            </audio>
                                        </template>
                                    </div>
                                </div>
                                <button v-on:click="clearReply">
                                    <i class="fa-regular fa-2xl fa-circle-xmark"></i>
                                </button>
                            </div>
                            <ul v-if="showQuickReplies" class="flex-grow bg-white shadow-md rounded-lg mt-2 p-2">
                                <li v-for="(reply, index) in filteredQuickReplies"
                                    :key="index"
                                    v-on:click="selectQuickReply(index)"
                                    :class="{
                                    'bg-blue-100 text-blue-900': index === quickReplyIndex,
                                    'hover:bg-gray-100 cursor-pointer rounded-md p-2 transition-all duration-200 ease-in-out': true
                                }">
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- reply reaction messages  section end -->

                    <!-- Preview section Start -->
                    <div class="relative">
                        <div class="absolute bottom-[5px] ">
                            <div v-if="wb_imageAttachment || wb_videoAttachment || wb_documentAttachment" class="flex flex-wrap gap-4 p-4">
                                <!-- Image Attachment -->
                                <div v-if="wb_imageAttachment" class="relative flex flex-col items-center py-6 px-4 bg-[#F0F2F5] border border-gray-300 rounded-lg shadow-lg max-w-[250px]">
                                    <!-- Preview Text -->
                                    <span class="text-xs font-semibold text-gray-500 mb-2"><?php echo _l('preview'); ?></span>
                                    <button v-on:click="wb_removeImageAttachment" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 mt-1 focus:outline-none">
                                        <i class="fa fa-times fa-lg"></i>
                                    </button>
                                    <img :src="wb_imagePreview" alt="Selected Image" class="w-full h-28 object-cover rounded-md mb-3 shadow-sm" />
                                    <span class="mt-2 text-sm font-medium text-gray-700 truncate w-full text-center">{{ wb_imageAttachment.name }}</span>
                                </div>

                                <!-- Video Attachment -->
                                <div v-if="wb_videoAttachment" class="relative flex flex-col items-center py-6 px-4 bg-[#F0F2F5] border border-gray-300 rounded-lg shadow-md max-w-[280px]">
                                    <!-- Preview Text -->
                                    <span class="text-xs font-semibold text-gray-500 mb-2"><?php echo _l('preview'); ?></span>
                                    <button v-on:click="wb_removeVideoAttachment" class="absolute top-2 right-2 text-red-500 hover:text-red-700 focus:outline-none">
                                        <i class="fa fa-times "></i>
                                    </button>
                                    <video :src="wb_videoPreview" controls class="w-full object-cover rounded-md"></video>
                                    <span class="mt-2 text-sm text-gray-700 truncate w-full text-center">{{ wb_videoAttachment.name }}</span>
                                </div>

                                <!-- Document Attachment -->
                                <div v-if="wb_documentAttachment" class="relative flex flex-col items-center p-2 bg-[#F0F2F5] border border-gray-300 rounded-lg shadow-md max-w-[250px]">
                                    <!-- Preview Text -->
                                    <span class="text-xs font-semibold text-gray-500 mb-2"><?php echo _l('preview'); ?></span>
                                    <button v-on:click="wb_removeDocumentAttachment" class="absolute top-2 right-2 text-red-500 hover:text-red-700 focus:outline-none">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <i class="fa fa-file text-gray-600 text-4xl"></i>
                                    <span class="mt-2 text-sm text-gray-700 truncate w-full text-center">{{ wb_documentAttachment.name }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- Preview section End -->
                    </div>


                    <!-- reply section start-->
                    <div v-if="wb_selectedinteraction && wb_selectedinteraction.messages" class="right-bottom w-full top-full sticky flex justify-between items-center px-4 py-2 space-x-2 bg-[#ffffff] mt-[-50px]">
                        <form v-on:submit.prevent="wb_sendMessage" class="flex flex-col w-full">

                            <!-- Input Field at the Top -->
                            <div class="w-full bg-gray-100 rounded-lg px-4 py-2  text-sm">
                                <input type="text"
                                    v-model="wb_newMessage" ref="inputField" placeholder="<?= _l('type_your_message') ?>" class="mentionable w-full bg-transparent focus:outline-none px-2 py-2" id="wb_newMessage">
                            </div>


                            <div class="flex justify-between items-center space-x-4">
                                <!-- Left Side Icon Column -->

                                <div class="flex space-x-2 items-center">
                                    <!-- OpenAI Button (if enabled) -->
                                    <?php if (get_option('enable_wb_openai')) { ?>

                                        <div class="dropup" tabindex="0" data-toggle="tooltip" data-title='<?php echo _l('ai_prompt_note'); ?>'>
                                            <button class="btn dropdown-toggle p-2" :class="{ 'disabled': !isButtonEnabled }" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa-solid fa-robot text-violet-500 text-xl"></i>
                                            </button>

                                            <ul class="dropdown-menu w-[300px]" aria-labelledby="dropdownMenu2">
                                                <!-- Menu Items -->
                                                <li class="dropdown-header">
                                                    <span class="tw-mr-1"><i class="fa-solid fa-robot text-sky-500"></i></span><?= _l('ai_prompt') ?>
                                                </li>
                                                <li role="separator" class="divider"></li>
                                                <li class="dropdown dropdown-submenu">
                                                    <a href="javascript:;"><i class="fa-solid fa-headset text-sky-500 mr-2"></i><?= _l('change_tone') ?></a>
                                                    <ul class="dropdown-menu" style="top: 35px;margin-top: -140px;">
                                                        <li v-on:click="wb_handleItemClick('<?= _l('change_tone') ?>', '<?= _l('professional') ?>')"><a href="javascript:;"><?= _l('professional') ?></a></li>
                                                        <li v-on:click="wb_handleItemClick('<?= _l('change_tone') ?>', '<?= _l('friendly') ?>')"><a href="javascript:;"><?= _l('friendly') ?></a></li>
                                                        <li v-on:click="wb_handleItemClick('<?= _l('change_tone') ?>', '<?= _l('empathetic') ?>')"><a href="javascript:;"><?= _l('empathetic') ?></a></li>
                                                        <li v-on:click="wb_handleItemClick('<?= _l('change_tone') ?>', '<?= _l('straightforward') ?>')"><a href="javascript:;"><?= _l('straightforward') ?></a></li>
                                                    </ul>
                                                </li>
                                                <li class="dropdown-submenu">
                                                    <a href="javascript:;">
                                                        <i class="fa-solid fa-language text-info tw-mr-2"></i>
                                                        <?php echo _l('translate'); ?>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu" style="top: 35px; margin-top: -350px;">
                                                        <li>
                                                            <input type="text" class="form-control" style="border-radius: 25px;" v-model="searchQuery" placeholder="<?= _l('search_language') ?>" />
                                                        </li>
                                                        <li v-for="lang in filteredLanguages" :key="lang">
                                                            <a href="javascript:;" v-on:click="wb_handleItemClick('Translate', lang)">
                                                                {{ ucfirst(lang) }}
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li v-on:click="wb_handleItemClick('<?php echo _l('fix_spelling_and_grammar'); ?>')"><a href="javascript:;"><i class="fa-solid fa-check text-info tw-mr-2"></i><?php echo _l('fix_spelling_and_grammar'); ?></a></li>
                                                <li v-on:click="wb_handleItemClick('<?php echo _l('simplify_language'); ?>')"><a href="javascript:;"><i class="fa-solid fa-virus text-info tw-mr-2"></i><?php echo _l('simplify_language'); ?></a></li>

                                                <li class="dropdown dropdown-submenu" v-if="customPrompts.length > 0">
                                                    <a href="javascript:;"><i class="fa-solid fa-reply text-sky-500 mr-2"></i><?= _l('custom_prompt') ?></a>
                                                    <ul class="dropdown-menu" style="top: 35px;margin-top: -80px;">
                                                        <li v-for="(prompt, index) in customPrompts" :key="index" v-if="shouldDisplayPrompt(prompt)" v-on:click="wb_handleItemClick('<?php echo _l('custom_prompt'); ?>', prompt.action)">
                                                            <a href="javascript:;">{{ prompt.label }}</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>

                                    <?php } ?>

                                    <!-- Emoji Button -->
                                    <button v-on:click="toggleEmojiPicker" id="emoji_btn" type="button" class="flex justify-center items-center  cursor-pointer">
                                        <i class="fa-regular fa-face-grin fa-xl" data-toggle="tooltip" data-title="<?= _l('emojis') ?>" data-placement="top"></i>
                                    </button>

                                    <!-- Attachment Button -->
                                    <button v-on:click="toggleAttachmentOptions" type="button" class="flex items-center justify-center p-2 text-gray-700 hover:text-blue-900 focus:outline-none" title="<?= _l('attach_image_video_docs') ?>" data-toggle="tooltip" data-title="<?= _l('attach_image_video_docs') ?>" data-placement="top">
                                        <i class="fa-solid fa-paperclip fa-xl"></i>
                                    </button>
                                    <!-- Display the canned replies list -->
                                    <ul v-if="cannedRepliesVisible && cannedReplies.length" class="absolute bottom-[108px] overflow-y-auto left-[120px] bg-white border border-gray-300 rounded-lg shadow-lg flex flex-col space-y-2 p-4 w-[500px] max-h-[400px]">
                                        <div class="bg-blue-500 text-white text-lg font-bold px-4 py-2 rounded">
                                            <?= _l('canned_replies') ?>
                                        </div>
                                        <li v-for="reply in cannedReplies"
                                            v-if="shoud_wb_cannedReplyData(reply)"
                                            :key="reply.title"
                                            class="relative flex flex-col px-3 py-2 hover:bg-gray-100 cursor-pointer border border-gray-300 rounded-md mb-2 shadow-sm"
                                            v-on:click="addToMessage(reply)">
                                            <div class="font-semibold text-gray-800 truncate w-[385px]">{{ reply.title }}</div>
                                            <div class="text-gray-600 text-sm truncate">{{ reply.description }}</div>
                                            <span
                                                v-if="reply.is_public === '1'"
                                                class="absolute top-[1px] right-2 bg-green-200 text-green-800 text-xs font-semibold px-2 py-1 rounded">
                                                <?= _l('public') ?>
                                            </span>
                                        </li>
                                    </ul>


                                    <!-- Canned Button -->
                                    <button v-if="cannedReplies.length > 0" v-on:click="toggleCannedReplies" ref="cannedRepliesDropdown" type="button" class="flex items-center justify-center p-2 text-gray-700 hover:text-blue-900 focus:outline-none" title="<?= _l('canned_reply') ?>" data-toggle="tooltip" data-title="<?= _l('attach_image_video_docs') ?>" data-placement="top">
                                        <i class="fa-regular fa-message fa-lg"></i>
                                    </button>

                                    <!-- Recording Button -->
                                    <button v-on:click="wb_toggleRecording" type="button" class="flex items-center justify-center p-2 text-gray-700 hover:text-gray-900 focus:outline-none" title="<?= _l('record_audio') ?>">
                                        <span v-if="!wb_recording" class="fa fa-microphone text-xl" aria-hidden="true" data-toggle="tooltip" data-title="<?= _l('record_audio') ?>" data-placement="top"></span>
                                        <span v-else class="fa fa-stop text-xl" aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div class="flex justify-end items-center gap-4">
                                    <div class="text-sm text-gray-500 font-semibold"><?php echo _l('use_@_to_add_merge_fields'); ?></div>
                                    <!-- Send Button (conditionally visible) -->
                                    <button v-if="wb_showSendButton || wb_audioBlob" type="submit" class="flex items-center justify-center p-2 bg-green-500 rounded-full focus:outline-none">
                                        <i class="fas fa-paper-plane text-white" aria-hidden="true"></i>
                                    </button>
                                </div>

                                <div class="absolute bottom-[100px]">
                                    <!-- Attachment Options Dropdown (conditionally visible) -->
                                    <div v-if="showAttachmentOptions" class="flex flex-col gap-2 text-nowrap bg-[#F0F2F5] shadow-lg rounded-lg p-2">
                                        <input type="file" id="imageAttachmentInput" ref="imageAttachmentInput" v-on:change="wb_handleImageAttachmentChange"
                                            accept="<?= wb_get_allowed_extension()['image']['extension'] ?>" class="hidden">
                                        <label for="imageAttachmentInput" class="cursor-pointer flex items-center p-2 text-gray-700 hover:text-gray-900">
                                            <i class="fa-regular text-blue-500 fa fa-image mr-2 fa-lg" aria-hidden="true"></i><span><?= _l('send_image') ?></span>
                                        </label>

                                        <input type="file" id="videoAttachmentInput" ref="videoAttachmentInput" v-on:change="wb_handleVideoAttachmentChange"
                                            accept="<?= wb_get_allowed_extension()['video']['extension'] ?>" class="hidden">
                                        <label for="videoAttachmentInput" class="cursor-pointer flex items-center p-2 text-gray-700 hover:text-gray-900">
                                            <i class="fa fa-video text-green-500 mr-2 fa-lg" aria-hidden="true"></i><span><?= _l('send_video') ?></span>
                                        </label>

                                        <input type="file" id="documentAttachmentInput" ref="documentAttachmentInput" v-on:change="wb_handleDocumentAttachmentChange"
                                            accept="<?= wb_get_allowed_extension()['document']['extension'] ?>" class="hidden">
                                        <label for="documentAttachmentInput" class="cursor-pointer flex items-center p-2 text-gray-700 hover:text-gray-900">
                                            <i class="fa-regular text-yellow-500 fa fa-file mr-2 fa-lg" aria-hidden="true"></i><span><?= _l('send_document') ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="emoji-picker-container" ref="emojiPickerContainer"></div>
                            <input type="hidden" name="rel_type" id="rel_type" value="">
                        </form>
                    </div>
                    <!-- reply section end-->
                </div>
                <!-- Main content end-->
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/tailwind.css.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/vue.min.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/axios.min.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/recorder-core.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/purify.min.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/mp3-engine.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/mp3.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/emoji-mart.min.js'); ?>"></script>

<script>
    "use strict";
    $(document).on('click', '.hideMessage', function() {
        $(this).parent().addClass('hide');
    });

    $(function() {
        $(document).on('mouseenter', '.side-chat', function() {
            $(this).find('.dele-icn').removeClass('hide');
        });

        $(document).on('mouseleave', '.side-chat', function() {
            $(this).find('.dele-icn').addClass('hide');
        });
    })

    new Vue({
        el: '#app',
        data() {
            return {
                interactions: [],
                previousCounts: {},
                wb_selectedinteractionIndex: null,
                wb_selectedinteraction: null,
                wb_selectedinteractionMobNo: null,
                wb_selectedinteractionSenderNo: null,
                wb_newMessage: '',
                wb_agentId: '',
                wb_selectedStaffId: '',
                wb_selectedinteractionId: null,
                wb_imageAttachment: null,
                wb_videoAttachment: null,
                wb_documentAttachment: null,
                wb_imagePreview: '',
                wb_videoPreview: '',
                wb_csrfToken: '<?php echo $csrfToken; ?>',
                wb_recording: false,
                wb_audioBlob: null,
                wb_recordedAudio: null,
                errorMessage: '',
                sameNumErrorMessage: '',
                wb_searchText: '',
                wb_login_staff_id: '<?= get_staff_user_id(); ?>',
                wb_selectedWaNo: '<?= get_option("wac_default_phone_number") ?>', // Define wb_selectedWaNo variable
                wb_default_number: '<?= get_option("wac_default_phone_number") ?>',
                wb_filteredInteractions: [], // Define wb_filteredInteractions to store filtered interactions
                wb_displayedInteractions: [],
                wb_showEmojiPicker: false,
                isLoading: false,
                showQuickReplies: false,
                filteredQuickReplies: [],
                languages: <?= json_encode(config_item('languages')); ?>,
                searchQuery: '',
                showAttachmentOptions: false,
                replyingToMessage: null,
                cannedReplies: [],
                customPrompts: [],
                cannedRepliesVisible: true,
                has_pemission_view_canned_reply: '<?= staff_can('view', 'wtc_canned_reply'); ?>',
                has_pemission_view_ai_prompts: '<?= staff_can('view', 'wtc_ai_prompts'); ?>'
            };
        },

        methods: {
            wb_fetchCustomPrompts() {
                $.ajax({
                    url: `${admin_url}whatsbot/ai_prompts/get`,
                    type: 'POST',
                    dataType: 'json',
                    success: (response) => {
                        if (Array.isArray(response.custom_prompts)) {
                            this.customPrompts = response.custom_prompts.map(prompt => ({
                                label: prompt.name,
                                action: prompt.action,
                                is_public: prompt.is_public,
                                added_from: prompt.added_from

                            }));
                        } else {
                            console.error('Invalid response structure', response);
                        }
                    },
                    error: (error) => {
                        console.error('Error fetching canned replies:', error);
                    }
                });
            },
            shouldDisplayPrompt(prompt) {
                return this.wb_login_staff_id === prompt.added_from || this.has_pemission_view_ai_prompts;
            },

            toggleCannedReplies() {
                this.cannedRepliesVisible = !this.cannedRepliesVisible;
                if (this.cannedRepliesVisible) {
                    // Add event listener when dropdown opens
                    document.addEventListener('click', this.handleClickOutside);
                }
            },
            handleClickOutside(event) {
                const dropdown = this.$refs.cannedRepliesDropdown;

                if (dropdown && !dropdown.contains(event.target)) {
                    // If the click is outside the dropdown, close it
                    this.cannedRepliesVisible = false;
                    document.removeEventListener('click', this.handleClickOutside); // Remove listener when dropdown closes
                }
            },
            beforeDestroy() {
                document.removeEventListener('click', this.handleClickOutside);
            },
            wb_cannedReplyData() {
                $.ajax({
                    url: `${admin_url}whatsbot/canned_reply/get`,
                    type: 'POST',
                    dataType: 'html',
                    success: (response) => {
                        const parsedResponse = JSON.parse(response);
                        if (parsedResponse.reply_data && Array.isArray(parsedResponse.reply_data)) {
                            this.cannedReplies = parsedResponse.reply_data.map(reply => ({
                                title: reply.title,
                                description: reply.description,
                                is_public: reply.is_public,
                                added_from: reply.added_from
                            }));
                            this.cannedRepliesVisible = false;
                        } else {
                            console.error('Invalid response structure');
                        }
                    },
                    error: (error) => {
                        console.error('Error fetching canned replies:', error);
                    }
                });
            },
            shoud_wb_cannedReplyData(reply) {
                return reply.is_public === '1' || this.wb_login_staff_id === reply.added_from || this.has_pemission_view_canned_reply;
            },
            addToMessage(reply) {
                this.wb_newMessage = `${reply.description}`;
                this.cannedRepliesVisible = false;
                this.$refs.inputField.focus();
            },

            wb_selectinteraction(id) {
                $.ajax({
                    url: `${admin_url}whatsbot/chat_mark_as_read`,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'interaction_id': id
                    },
                })
                const index = this.interactions.findIndex(interaction => interaction.id === id);
                if (index !== -1) {
                    this.wb_selectedinteractionIndex = index;
                    this.wb_selectedinteraction = this.interactions[index];
                    this.wb_selectedinteractionId = this.wb_selectedinteraction['id'];
                    this.wb_selectedinteractionMobNo = this.wb_selectedinteraction['receiver_id'];
                    this.wb_selectedinteractionSenderNo = this.wb_selectedinteraction['wa_no'];
                    this.wb_scrollToBottom();
                    this.wb_fetchCustomPrompts();
                    this.wb_cannedReplyData();

                    this.$nextTick(() => {
                        $('#rel_type').val(this.wb_selectedInteraction['type']);
                        $('#rel_type').trigger('change');
                    });
                }
            },
            sanitizeMessage(message) {
                return DOMPurify.sanitize(message, {
                    USE_PROFILES: {
                        html: true
                    }
                });
            },

            trimMessage(message, maxLength = 100) {
                const sanitizedMessage = this.sanitizeMessage(message);
                if (sanitizedMessage.length > maxLength) {
                    return sanitizedMessage.substring(0, maxLength) + '...';
                }
                return sanitizedMessage;
            },

            getOriginalMessage(refMessageId) {
                const message = this.wb_selectedinteraction.messages.find(msg => msg.message_id === refMessageId) || {};
                return {
                    ...message,
                    message: this.trimMessage(message.message),
                    assets_url: message.asset_url || ''
                };
            },

            replyToMessage(message) {
                this.replyingToMessage = message || message.asset_url;
            },
            clearReply() {
                this.replyingToMessage = null;
            },
            wb_initAgent() {
                const agentId = this.wb_selectedinteraction.agent.agent_id;
                this.selectedStaffId = agentId;
                $('#AgentModal').modal('show');
                setTimeout(function() {
                    $('#AgentModal').find('select[name="assigned[]"]').val(agentId);
                    $('#AgentModal').find('select[name="assigned[]"]').trigger('change');
                }, 100);
            },
            wb_handleAssignedChange(event) {
                const id = this.wb_selectedinteraction ? this.wb_selectedinteraction.id : null;
                const staffId = $('select[name="assigned[]"]').val();
                $.ajax({
                    url: `${admin_url}whatsbot/assign_staff`,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'staff_id': staffId,
                        'interaction_id': id
                    },
                })
                this.wb_selectinteraction(id);
            },

            wb_deleteInteraction(id) {
                if (confirm_delete()) {
                    $.ajax({
                        url: `${admin_url}whatsbot/delete_chat`,
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            'interaction_id': id
                        },
                    }).done(function(res) {
                        if (res) {
                            alert_float('danger', "<?= _l('deleted', _l('chat')); ?>");
                        }
                    });
                }
            },

            async wb_sendMessage() {
                if (this.wb_default_number != this.wb_selectedinteraction.wa_no) {
                    this.sameNumErrorMessage = "<?= _l('you_cannot_send_a_message_using_this_number') ?>";
                    return;
                }

                if (!this.wb_selectedinteraction) return;
                const wb_formData = new FormData();
                wb_formData.append('id', this.wb_selectedinteraction.id);
                wb_formData.append('to', this.wb_selectedinteraction.receiver_id);
                wb_formData.append('csrf_token_name', this.wb_csrfToken);
                wb_formData.append('type', this.wb_selectedinteraction.type);
                wb_formData.append('type_id', this.wb_selectedinteraction.type_id);
                const MAX_MESSAGE_LENGTH = 2000;
                if (this.wb_newMessage.length > MAX_MESSAGE_LENGTH) {
                    this.wb_newMessage = this.wb_newMessage.substring(0, MAX_MESSAGE_LENGTH);

                }
                // Add message if it exists
                if (this.wb_newMessage.trim()) {
                    wb_formData.append('message', DOMPurify.sanitize(this.wb_newMessage));
                }

                // Handle image attachment
                if (this.wb_imageAttachment) {
                    wb_formData.append('image', this.wb_imageAttachment);
                }

                // Handle video attachment
                if (this.wb_videoAttachment) {
                    wb_formData.append('video', this.wb_videoAttachment);
                }

                // Handle document attachment
                if (this.wb_documentAttachment) {
                    wb_formData.append('document', this.wb_documentAttachment);
                }

                // Handle audio attachment
                if (this.wb_audioBlob) {
                    wb_formData.append('audio', this.wb_audioBlob, 'audio.mp3');
                }
                if (this.replyingToMessage) {
                    wb_formData.append('ref_message_id', this.replyingToMessage.message_id);
                }

                try {
                    const wb_response = await axios.post('<?php echo admin_url('whatsbot/whatsapp_webhook/send_message'); ?>', wb_formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    // Clear attachments
                    this.wb_newMessage = '';
                    this.wb_imageAttachment = null;
                    this.wb_videoAttachment = null;
                    this.wb_documentAttachment = null;
                    this.wb_audioBlob = null;
                    this.wb_imagePreview = '';
                    this.wb_videoPreview = '';
                    this.wb_filterInteractions();
                    this.wb_selectinteraction(this.wb_selectedinteraction.id);
                    this.errorMessage = '';
                    this.clearReply();
                    this.wb_scrollToBottom();
                    this.wb_selectedinteractionIndex = 0;
                } catch (error) {
                    const wb_rawErrorMessage = error.response && error.response.data ? error.response.data : 'An error occurred. Please try again.';
                    // Define regular expressions to match the desired parts of the HTML error message
                    const wb_typeRegex = /<p>Type: (.+)<\/p>/;
                    const wb_messageRegex = /<p>Message: (.+)<\/p>/;

                    // Extract the type and message from the HTML error message
                    const wb_typeMatch = wb_rawErrorMessage.match(wb_typeRegex);
                    var wb_messageMatch = wb_rawErrorMessage.match(wb_messageRegex);
                    if (wb_messageMatch != null) {
                        if (typeof(wb_messageMatch[1] == 'object')) {
                            wb_messageMatch[1] = JSON.parse(wb_messageMatch[1]);
                            wb_messageMatch[1] = wb_messageMatch[1].error.message;
                        }
                    }
                    const wb_getTypeText = wb_typeMatch ? wb_typeMatch[1] : '';
                    const wb_getMessageText = wb_messageMatch ? wb_messageMatch[1] : '';
                    // Construct the error message by concatenating the extracted text content
                    const errorMessage = wb_getTypeText.trim() + '\n' + wb_getMessageText.trim();
                    this.errorMessage = errorMessage;
                }
            },

            async wb_fetchinteractions() {
                try {
                    const staff_id = this.wb_login_staff_id;
                    const wb_response = await fetch('<?php echo admin_url('whatsbot/interactions'); ?>');
                    const data = await wb_response.json();
                    const enable_supportagent = "<?= get_option('enable_supportagent') ?>";

                    if (data && data.interactions) {

                        const isAdmin = <?php echo is_admin() ? 'true' : 'false'; ?>;

                        if (!isAdmin && enable_supportagent == 1) {
                            this.interactions = data.interactions.filter(interaction => {

                                const chatagent = interaction.agent;
                                if (chatagent) {

                                    const agentIds = Array.isArray(chatagent.agent_id) ? chatagent.agent_id : [chatagent.agent_id];
                                    const assignIds = Array.isArray(chatagent.assign_id) ? chatagent.assign_id : [chatagent.assign_id];

                                    // Check if `staff_id` is included in either `agentIds` or `assignIds`
                                    return agentIds.includes(staff_id) || assignIds.includes(staff_id);
                                }
                                return false;
                            });
                        } else {

                            this.interactions = data.interactions;
                        }

                    } else {
                        this.interactions = [];
                    }
                    this.wb_filterInteractions();
                    this.wb_updateSelectedInteraction();
                } catch (error) {
                    console.error('Error fetching interactions:', error);
                }
            },
            wb_updateSelectedInteraction() {
                const wb_new_index = this.interactions.findIndex(interaction => interaction.receiver_id === this.wb_selectedinteractionMobNo && interaction.wa_no === this.wb_selectedinteractionSenderNo && interaction.id === this.wb_selectedinteractionId);
                this.wb_selectedinteraction = this.interactions[wb_new_index];
            },

            wb_getTime(timeString) {
                const date = new Date(timeString);
                const hour = date.getHours();
                const minute = date.getMinutes();
                const period = hour < 12 ? 'AM' : 'PM';
                const formattedHour = hour % 12 || 12;
                return `${formattedHour}:${minute < 10 ? '0' + minute : minute} ${period}`;
            },

            getDate(dateString) {
                const wb_date = new Date(dateString);
                const wb_options = {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                };
                return wb_date.toLocaleDateString('en-GB', wb_options).replace(' ', '-').replace(' ', '-');
            },

            wb_shouldShowDate(currentMessage, previousMessage) {
                if (!previousMessage) return true;
                return this.getDate(currentMessage.time_sent) !== this.getDate(previousMessage.time_sent);
            },

            wb_scrollToBottom() {
                this.$nextTick(() => {
                    const wb_chatContainer = this.$refs.wb_chatContainer;
                    if (wb_chatContainer) {
                        wb_chatContainer.scrollTop = wb_chatContainer.scrollHeight;
                    }
                });
            },

            wb_getAvatarInitials(name) {
                const wb_words = name.split(' ');
                const wb_initials = wb_words.slice(0, 2).map(word => word.charAt(0)).join('');
                return wb_initials.toUpperCase();
            },
            playNotificationSound() {
                var enableSound = "<?= get_option('enable_wtc_notification_sound') ?>";

                if (enableSound == 1) {
                    var audio = new Audio('<?= module_dir_url('whatsbot', 'assets/audio/whatsapp_notification.mp3') ?>');
                    audio.play();
                }
            },
            wb_countUnreadMessages(interactionId) {
                const interaction = this.interactions.find(inter => inter.id === interactionId);
                if (interaction) {
                    return interaction.messages.filter(message => message.is_read == 0).length;
                }
                return 0;
            },

            async wb_toggleRecording() {
                if (!this.wb_recording) {

                    this.wb_startRecording();
                } else {

                    this.wb_stopRecording();
                }
            },
            wb_startRecording() {
                // Initialize Recorder.js if not already initialized
                if (!this.recorder) {
                    this.recorder = new Recorder({
                        type: "mp3",
                        sampleRate: 16000,
                        bitRate: 16,
                        onProcess: (buffers, powerLevel, bufferDuration, bufferSampleRate) => {

                        }
                    });
                }
                this.recorder.open((stream) => {
                    this.wb_recording = true;
                    this.recorder.start();
                }, (err) => {
                    console.error("Failed to start wb_recording:", err);
                });
            },

            wb_stopRecording() {
                if (this.recorder && this.wb_recording) {
                    this.recorder.stop((blob, duration) => {
                        this.recorder.close();
                        this.wb_recording = false;
                        this.wb_audioBlob = blob;
                        this.wb_sendMessage();
                        this.wb_recordedAudio = URL.createObjectURL(blob);
                    }, (err) => {
                        console.error("Failed to stop wb_recording:", err);

                    });
                }
            },
            wb_handleImageAttachmentChange(event) {
                this.wb_imageAttachment = event.target.files[0];
                this.wb_imagePreview = URL.createObjectURL(this.wb_imageAttachment);
                this.showAttachmentOptions = false;
            },
            wb_handleVideoAttachmentChange(event) {
                this.wb_videoAttachment = event.target.files[0];
                this.wb_videoPreview = URL.createObjectURL(this.wb_videoAttachment);
                this.showAttachmentOptions = false;
            },
            wb_handleDocumentAttachmentChange(event) {
                this.wb_documentAttachment = event.target.files[0];
                this.showAttachmentOptions = false;
            },
            wb_removeImageAttachment() {
                this.wb_imageAttachment = null;
                this.wb_imagePreview = '';
            },
            wb_removeVideoAttachment() {
                this.wb_videoAttachment = null;
                this.wb_videoPreview = '';
            },
            wb_removeDocumentAttachment() {
                this.wb_documentAttachment = null;
            },
            wb_formatTime(timestamp) {
                const currentDate = new Date();
                const messageDate = new Date(timestamp);
                const diffInMs = currentDate - messageDate;
                const diffInHours = diffInMs / (1000 * 60 * 60);

                if (diffInHours < 24) {
                    // Less than 24 hours, display time
                    const hour = messageDate.getHours();
                    const minute = messageDate.getMinutes();
                    const period = hour < 12 ? 'AM' : 'PM';
                    const formattedHour = hour % 12 || 12;
                    return `${formattedHour}:${minute < 10 ? '0' + minute : minute} ${period}`;
                } else {
                    // More than 24 hours, display wb_date in dd-mm-yy format
                    const day = messageDate.getDate();
                    const month = messageDate.getMonth() + 1;
                    const year = messageDate.getFullYear() % 100; // Get last two digits of the year
                    return `${day}-${month < 10 ? '0' + month : month}-${year}`;
                }
            },
            wb_alertTime(lastMsgTime) {
                const timezone = "<?= get_option('default_timezone'); ?>"; // Set the desired timezone
                if (lastMsgTime) {
                    // Parse the last message time in the given timezone
                    const messageDate = new Date(lastMsgTime);

                    // Get the current date and time in the specified timezone
                    const currentDate = new Date(new Date().toLocaleString("en-US", {
                        timeZone: timezone
                    }));

                    const diffInMs = currentDate - messageDate;
                    const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60)); // Round down to nearest hour
                    const diffInMinutes = Math.floor((diffInMs % (1000 * 60 * 60)) / (1000 * 60)); // Calculate remaining minutes

                    // Check if the difference is less than 24 hours
                    if (diffInHours < 24) {
                        // Calculate remaining time within 24 hours
                        const remainingHours = 23 - diffInHours; // Subtract one hour from 24
                        const remainingMinutes = 60 - diffInMinutes;
                        return `Reply within ${remainingHours} hours and ${remainingMinutes} minutes`;
                    } else {
                        return null;
                    }
                } else {
                    return lastMsgTime;
                }
            },
            wb_stripHTMLTags(str) {
                return str.replace(/<\/?[^>]+(>|$)/g, "");
            },
            wb_truncateText(text, wordLimit) {
                const strippedText = this.wb_stripHTMLTags(text);
                const wb_words = strippedText.split(' ');
                if (wb_words.length > wordLimit) {
                    return wb_words.slice(0, wordLimit).join(' ') + '...';
                }
                return text;
            },
            wb_filterInteractions() {
                let filtered = this.interactions;

                if (this.wb_selectedWaNo !== "*") {
                    filtered = filtered.filter(interaction => interaction.wa_no === this.wb_selectedWaNo);
                }
                this.wb_filteredInteractions = filtered;
                this.wb_searchInteractions(); // Call wb_searchInteractions to apply the search text filter
            },
            wb_searchInteractions() {
                if (this.wb_searchText) {
                    this.wb_displayedInteractions = this.wb_filteredInteractions.filter(interaction =>
                        interaction.name.toLowerCase().includes(this.wb_searchText.toLowerCase())
                    );
                } else {
                    this.wb_displayedInteractions = this.wb_filteredInteractions;
                }
            },
            wb_handleItemClick(menu, submenu = null) {
                const input_msg = this.wb_newMessage;
                this.isLoading = true;
                $.ajax({
                    url: '<?= site_url('whatsbot/ai_response') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        menu: menu,
                        submenu: submenu,
                        input_msg: input_msg,
                    },
                    success: (response) => {
                        if (response.status === false) {
                            alert_float('danger', response.message);
                        } else {
                            this.wb_newMessage = response.message || input_msg;
                            this.$nextTick(() => {
                                const input = this.$refs.inputField;
                                input.focus();
                            });
                        }
                        this.isLoading = false;
                    },
                })
            },
            ucfirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            },
            toggleEmojiPicker() {
                this.wb_showEmojiPicker = !this.wb_showEmojiPicker;
                if (this.wb_showEmojiPicker) {
                    this.initEmojiPicker();
                } else {
                    this.removeEmojiPicker();
                }
            },
            initEmojiPicker() {
                const container = document.getElementById('emoji-picker-container');
                container.innerHTML = '';
                const pickerOptions = {
                    onEmojiSelect: (emoji) => {
                        this.wb_newMessage += emoji.native;
                    }
                };
                const picker = new EmojiMart.Picker(pickerOptions);
                container.appendChild(picker);
                const input = document.getElementById('wb_newMessage');
                const rect = input.getBoundingClientRect();
                const containerRect = container.getBoundingClientRect();
                container.style.position = 'absolute';
                container.style.top = "-435px";
                container.style.left = "50px";
                document.addEventListener('click', this.handleClickOutside);
            },
            removeEmojiPicker() {
                const container = this.$refs.emojiPickerContainer;
                if (container) {
                    container.innerHTML = '';
                }
                document.removeEventListener('click', this.handleClickOutside);
            },
            handleClickOutside(event) {

                const emojiContainer = this.$refs.emojiPickerContainer;

                if (
                    (emojiContainer && !emojiContainer.contains(event.target) && !event.target.closest('#emoji_btn'))
                ) {
                    this.wb_showEmojiPicker = false;
                    this.removeEmojiPicker();
                    document.removeEventListener('click', this.handleClickOutside);
                }
            },
            toggleAttachmentOptions() {
                this.showAttachmentOptions = !this.showAttachmentOptions;
            },

        },
        watch: {
            wb_displayedInteractions(newInteractions) {
                newInteractions.forEach(interaction => {
                    const previousCount = this.previousCounts[interaction.id] || 0;
                    const currentCount = this.wb_countUnreadMessages(interaction.id);

                    if (currentCount > previousCount) {
                        this.playNotificationSound();
                    }

                    this.$set(this.previousCounts, interaction.id, currentCount);
                });
            }
        },
        created() {
            this.wb_fetchinteractions();
            setInterval(() => {
                this.wb_fetchinteractions();
            }, 5000);
            setInterval(() => {
                init_selectpicker();
            }, 2000);
        },
        computed: {
            overdueAlert() {
                const lastMsgTime = this.wb_selectedinteraction.last_msg_time;
                if (lastMsgTime) {
                    const currentDate = new Date();
                    const messageDate = new Date(lastMsgTime);
                    const diffInHours = Math.floor((currentDate - messageDate) / (1000 * 60 * 60));

                    if (diffInHours >= 24) {
                        return `
							<div class="flex items-center bg-amber-100 border border-yellow-400 w-full text-amber-700 px-4 py-3 rounded relative mt-4" role="alert">
							<i class="fas fa-exclamation-triangle mr-2 fa-xl text-amber-700"></i>
							<span class="block sm:inline"><span class="font-semibold text-amber-700">24 hours limit</span> WhatsApp does not allow sending messages 24 hours after they last messaged you. However, you can send them a template message.</span>
						</div> `;
                    }
                }
                return null;
            },
            wb_selectedInteraction() {
                return this.wb_selectedinteractionIndex !== null ? this.interactions[this.wb_selectedinteractionIndex] : null;
            },
            wb_showSendButton() {
                return this.wb_imageAttachment || this.wb_videoAttachment || this.wb_documentAttachment || this.wb_newMessage.trim();
            },

            isButtonEnabled() {
                return this.wb_newMessage.trim().length > 0;
            },
            wb_uniqueWaNos() {
                // Create a Set to store unique wa_no values
                const wb_uniqueWaNos = new Set();
                // Filter out interactions with duplicate wa_no values
                return this.interactions.filter(interaction => {
                    if (!wb_uniqueWaNos.has(interaction.wa_no)) {
                        wb_uniqueWaNos.add(interaction.wa_no);

                        return true;
                    }
                    return false;
                });
            },
            filteredLanguages() {
                return this.languages.filter(lang => lang.toLowerCase().includes(this.searchQuery.toLowerCase()));
            },
        },
    });
</script>
