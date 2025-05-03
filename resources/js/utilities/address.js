const GlobalAddress = {
    baseUrl: window.location.origin,

    loadUserAddress(addressId) {
        const provinceSelector = "#province";
        const districtSelector = "#district";
        const wardSelector = "#ward";
        const addressDetailSelector = "#address_detail";

        if (addressId) {
            console.log("Load address ID:", addressId);
            $.ajax({
                type: "GET",
                url: `${this.baseUrl}/api/address/detail-default`,
                data: { id: addressId },
                dataType: "json",
                success: (response) => {
                    if (response) {
                        this.renderAddressSelectors({
                            provinceSelector,
                            districtSelector,
                            wardSelector,
                            addressDetailSelector,
                            provinceId: response.province_id,
                            districtId: response.district_id,
                            wardId: response.ward_id,
                            addressDetail: response.address_detail,
                        });
                    }
                },
                error: (xhr) => {
                    console.error("Có lỗi khi lấy dữ liệu địa chỉ.");
                    console.error(xhr.responseText);
                },
            });
        } else {
            this.renderAddressSelectors({
                provinceSelector,
                districtSelector,
                wardSelector,
                addressDetailSelector,
            });
        }
    },

    renderAddressSelectors({
        provinceSelector = "#province",
        districtSelector = "#district",
        wardSelector = "#ward",
        addressDetailSelector = "#address_detail",
        provinceId = null,
        districtId = null,
        wardId = null,
        addressDetail = "",
    }) {
        $(addressDetailSelector).val(addressDetail);

        // Load provinces
        $.ajax({
            type: "GET",
            url: `${this.baseUrl}/api/address/provinces`,
            dataType: "json",
            success: (provinces) => {
                $(provinceSelector).html(
                    '<option value="">Chọn Tỉnh/Thành phố</option>'
                );
                $.each(provinces, (i, province) => {
                    const selected =
                        province.id == provinceId ? "selected" : "";
                    $(provinceSelector).append(
                        `<option value="${province.id}" ${selected}>${province.name}</option>`
                    );
                });
                $(provinceSelector).prop("disabled", false);

                if (provinceId) {
                    this.loadDistricts(provinceId, districtId, {
                        districtSelector,
                        wardSelector,
                        wardId,
                    });
                }
            },
            error: () => {
                console.error("Có lỗi khi tải danh sách tỉnh.");
            },
        });
    },

    loadDistricts(
        provinceId,
        selectedDistrictId = null,
        {
            districtSelector = "#district",
            wardSelector = "#ward",
            wardId = null,
        }
    ) {
        $.ajax({
            type: "GET",
            url: `${this.baseUrl}/api/address/districts/${provinceId}`,
            dataType: "json",
            success: (districts) => {
                $(districtSelector).html(
                    '<option value="">Chọn Quận/Huyện</option>'
                );
                $.each(districts, (i, district) => {
                    const selected =
                        district.id == selectedDistrictId ? "selected" : "";
                    $(districtSelector).append(
                        `<option value="${district.id}" ${selected}>${district.name}</option>`
                    );
                });
                $(districtSelector).prop("disabled", false);

                if (selectedDistrictId) {
                    this.loadWards(selectedDistrictId, wardId, {
                        wardSelector,
                    });
                }
            },
            error: () => {
                console.error("Có lỗi khi tải danh sách quận.");
            },
        });
    },

    loadWards(districtId, selectedWardId = null, { wardSelector = "#ward" }) {
        $.ajax({
            type: "GET",
            url: `${this.baseUrl}/api/address/wards/${districtId}`,
            dataType: "json",
            success: (wards) => {
                $(wardSelector).html(
                    '<option value="">Chọn Phường/Xã</option>'
                );
                $.each(wards, (i, ward) => {
                    const selected =
                        ward.id == selectedWardId ? "selected" : "";
                    $(wardSelector).append(
                        `<option value="${ward.id}" ${selected}>${ward.name}</option>`
                    );
                });
                $(wardSelector).prop("disabled", false);
            },
            error: () => {
                console.error("Có lỗi khi tải danh sách phường.");
            },
        });
    },
};

window.GlobalAddress = GlobalAddress;
