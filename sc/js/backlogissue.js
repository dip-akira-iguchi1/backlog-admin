'use strict';

const url = 'https://dip-dev.backlog.jp/api/v2/';
// 1073968314 : HATA_FRONT
// 1073929701 : HA_AT
// 1073824244 : RFC
const hata_front = '1073968314';
const rfc_ha_at = ['1073929701','1073824244'];
const apiKey = 'xUvqAqpjyegIDjFKMyXH5dPfT3mubvthupEImyDPxCOX5YP5L3e8FsQfzeEkV2Yg'
const categoryId = '1074380939'; // うさぎ

// 自分の関係ありそうな担当者
const rfcAssigneeIds = ['1074230199','1074177895','1074493605','1074530191','1074390732'];
const haatAssigneeIds = ['1074452446','1074464638','1074481097','1074390732'];


// HATA_FRONTからとってくる
const getHATA_FRONT_Issues = async () => {
    const api = url + 'issues?apiKey=' + apiKey + '&projectId[]=1073968314&parentChild=1&categoryId[]=' + categoryId+ '&statusId[]=1&statusId[]=17538&statusId[]=2&statusId[]=3&statusId[]=17722&statusId[]=19080&statusId[]=17539&statusId[]=33258&count=100';
    console.log(api);
    return new Promise((resolve, reject) => {
        fetch(api).then(res => res.json()).then(data => {
            resolve(data);
        });
    });
}

// HATA_FRONTの子課題とってくる
const getHATA_FRONT_CHILD_Issues = async (issueId) => {

    const issueids = issueId.map((v) => `parentIssueId[]=${v}`).join('&');

    const api = url + 'issues?apiKey=' + apiKey + '&' + issueids + '&projectId[]=1073968314&categoryId[]=' + categoryId+ '&count=100';
    return new Promise((resolve, reject) => {
        fetch(api).then(res => res.json()).then(data => {
            resolve(data);
        });
    });
};


// RDCとHA_ATからとってくる
const getRFC_HAAT_Issues = async () => {
    const assigneeIds = haatAssigneeIds.map((v) => 'assigneeId[]='+v).join('&') + '&' + rfcAssigneeIds.map((v) => 'assigneeId[]='+v).join('&');
    const projectIds = rfc_ha_at.map((v) => 'projectId[]='+v).join('&');
    const api = url + 'issues?apiKey=' + apiKey + '&'+projectIds+'&'+assigneeIds+ '&statusId[]=1&statusId[]=2&statusId[]=3&count=100';
    return new Promise((resolve, reject) => {
        fetch(api).then(res => res.json()).then(data => {
            resolve(data);
        });
    });
}
