<!-- Popup Box Structure -->
<div id="popupBox" class="fixed inset-0 overflow-y-auto h-full w-full flex items-center justify-center hidden" style="z-index:10000; background-color:#66666650">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 id="popupTitle" class="text-lg leading-6 font-medium text-gray-900"></h3>
            <div class="mt-2 px-7 py-3">
                <p id="popupMessage" class="text-sm text-gray-500"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="popupCloseButton" class="cursor-pointer px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-green-300 hidden">閉じる (Close)</button>
                <button id="popupConfirmButton" class="cursor-pointer px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-red-300 mt-2 ">確認 (Confirm)</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
