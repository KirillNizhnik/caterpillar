(()=>{"use strict";var t=function(t,e){if(Array.isArray(t))return t;if(Symbol.iterator in Object(t))return function(t,e){var n=[],r=!0,u=!1,o=void 0;try{for(var i,s=t[Symbol.iterator]();!(r=(i=s.next()).done)&&(n.push(i.value),!e||n.length!==e);r=!0);}catch(t){u=!0,o=t}finally{try{!r&&s.return&&s.return()}finally{if(u)throw o}}return n}(t,e);throw new TypeError("Invalid attempt to destructure non-iterable instance")},e=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t};!function(n,r){var u,o=n.plugins.registerPlugin,i=n.editPost.PluginDocumentSettingPanel,s=n.components,a=s.PanelRow,c=s.DateTimePicker,f=s.CheckboxControl,l=s.SelectControl,m=s.FormTokenField,T=s.Spinner,p=n.element.Fragment,A=n.htmlEntities.decodeEntities,y=lodash,g=y.isEmpty,E=y.keys,d=y.compact,h=React.useEffect,_=n.url.addQueryArgs,b=n.data,F=b.useSelect,x=b.useDispatch,N=b.register,S=b.createReduxStore,D=b.select,v=n.apiFetch,I=function(t){for(var e=arguments.length,n=Array(e>1?e-1:0),u=1;u<e;u++)n[u-1]=arguments[u];var o;console&&r.isDebugEnabled&&(o=console).debug.apply(o,["[Future]",t].concat(n))},R=(u={futureAction:null,futureActionDate:0,futureActionEnabled:!1,futureActionTerms:[],futureActionTaxonomy:null,termsListByName:null,termsListById:null,taxonomyName:null,isFetchingTerms:!1},r&&r.postTypeDefaultConfig?(r.postTypeDefaultConfig.autoEnable&&(u.futureActionEnabled=!0),r.postTypeDefaultConfig.expireType&&(u.futureAction=r.postTypeDefaultConfig.expireType),r.defaultDate?u.futureActionDate=parseInt(r.defaultDate):u.futureActionDate=(new Date).getTime(),r.postTypeDefaultConfig.taxonomy&&(u.futureActionTaxonomy=r.postTypeDefaultConfig.taxonomy),r.postTypeDefaultConfig.terms&&(u.futureActionTerms=r.postTypeDefaultConfig.terms.split(",").map((function(t){return parseInt(t)}))),u):u);I("DEFAULT_STATE",R);var C=S("publishpress-future/store",{reducer:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:R,n=arguments[1];switch(n.type){case"SET_FUTURE_ACTION":return e({},t,{futureAction:n.futureAction});case"SET_FUTURE_ACTION_DATE":return e({},t,{futureActionDate:n.futureActionDate});case"SET_FUTURE_ACTION_ENABLED":return e({},t,{futureActionEnabled:n.futureActionEnabled});case"SET_FUTURE_ACTION_TERMS":return e({},t,{futureActionTerms:n.futureActionTerms});case"SET_FUTURE_ACTION_TAXONOMY":return e({},t,{futureActionTaxonomy:n.futureActionTaxonomy});case"SET_TERMS_LIST_BY_NAME":return e({},t,{termsListByName:n.termsListByName});case"SET_TERMS_LIST_BY_ID":return e({},t,{termsListById:n.termsListById});case"SET_TAXONOMY_NAME":return e({},t,{taxonomyName:n.taxonomyName})}return t},actions:{setFutureAction:function(t){return{type:"SET_FUTURE_ACTION",futureAction:t}},setFutureActionDate:function(t){return{type:"SET_FUTURE_ACTION_DATE",futureActionDate:t}},setFutureActionEnabled:function(t){return{type:"SET_FUTURE_ACTION_ENABLED",futureActionEnabled:t}},setFutureActionTerms:function(t){return{type:"SET_FUTURE_ACTION_TERMS",futureActionTerms:t}},setFutureActionTaxonomy:function(t){return{type:"SET_FUTURE_ACTION_TAXONOMY",futureActionTaxonomy:t}},setTermsListByName:function(t){return{type:"SET_TERMS_LIST_BY_NAME",termsListByName:t}},setTermsListById:function(t){return{type:"SET_TERMS_LIST_BY_ID",termsListById:t}},setTaxonomyName:function(t){return{type:"SET_TAXONOMY_NAME",taxonomyName:t}},setIsFetchingTerms:function(t){return{type:"SET_IS_FETCHING_TERMS",isFetchingTerms:t}}},selectors:{getFutureAction:function(t){return t.futureAction},getFutureActionDate:function(t){return t.futureActionDate},getFutureActionEnabled:function(t){return t.futureActionEnabled},getFutureActionTerms:function(t){return t.futureActionTerms},getFutureActionTaxonomy:function(t){return t.futureActionTaxonomy},getTermsListByName:function(t){return t.termsListByName},getTermsListById:function(t){return t.termsListById},getTaxonomyName:function(t){return t.taxonomyName},getIsFetchingTerms:function(t){return t.isFetchingTerms}}});N(C),o("publishpress-future-action",{render:function(){var e=F((function(t){return t("publishpress-future/store").getFutureAction()}),[]),n=F((function(t){return t("publishpress-future/store").getFutureActionDate()}),[]),u=F((function(t){return t("publishpress-future/store").getFutureActionEnabled()}),[]),o=F((function(t){return t("publishpress-future/store").getFutureActionTerms()}),[]),s=F((function(t){return t("publishpress-future/store").getFutureActionTaxonomy()}),[]),y=F((function(t){return t("publishpress-future/store").getTermsListByName()}),[]),b=F((function(t){return t("publishpress-future/store").getTermsListById()}),[]),N=F((function(t){return t("publishpress-future/store").getIsFetchingTerms()}),[]),S=x("publishpress-future/store"),C=S.setFutureAction,O=S.setFutureActionDate,B=S.setFutureActionEnabled,L=S.setFutureActionTerms,U=S.setFutureActionTaxonomy,w=S.setTermsListByName,M=S.setTermsListById,P=S.setTaxonomyName,k=S.setIsFetchingTerms,Y=x("core/editor").editPost,j=function(t){B(t);var e={enabled:t};t&&(C(R.futureAction),O(R.futureActionDate),L(R.futureActionTerms),U(R.futureActionTaxonomy),e.action=R.futureAction,e.date=R.futureActionDate,e.terms=R.futureActionTerms,e.taxonomy=R.futureActionTaxonomy,X()),H(e)},X=function(){I("fetchTerms","Fetching terms...");var t=D("publishpress-future/store").getFutureActionTaxonomy(),e=D("core/editor").getCurrentPostType(),n={},u={};k(!0),I("futureActionTaxonomy",t),!t&&"post"===e||"category"===t?(I("fetchTerms","Fetching categories..."),v({path:_("wp/v2/categories",{per_page:-1})}).then((function(t){I("list",t),t.forEach((function(t){n[t.name]=t,u[t.id]=t.name})),w(n),M(u),P(r.strings.category),k(!1)}))):(I("fetchTerms","Fetching taxonomies..."),v({path:_("publishpress-future/v1/taxonomies/"+e)}).then((function(e){I("taxonomies",e.taxonomies),e.taxonomies.length>0?v({path:_("wp/v2/taxonomies/"+t,{context:"edit",per_page:-1})}).then((function(t){I("taxAttributes",t),v({path:_("wp/v2/"+t.rest_base,{context:"edit",per_page:-1})}).then((function(e){I("terms",e),e.forEach((function(t){n[A(t.name)]=t,u[t.id]=A(t.name)})),w(n),M(u),P(A(t.name)),k(!1)}))})):I("fetchTerms","No taxonomies found")})))},H=function(r){var i={publishpress_future_action:{enabled:u,date:n,action:e,terms:o,taxonomy:s}},a=!0,c=!1,f=void 0;try{for(var l,m=Object.entries(r)[Symbol.iterator]();!(a=(l=m.next()).done);a=!0){var T=l.value,p=t(T,2),A=p[0],y=p[1];i.publishpress_future_action[A]=y}}catch(t){c=!0,f=t}finally{try{!a&&m.return&&m.return()}finally{if(c)throw f}}Y(i),I("editPostAttribute",r,i)};h((function(){var t;t=D("core/editor").getEditedPostAttribute("publishpress_future_action"),I("fetchFutureActionData",t),B(t.enabled).then(undefined),C(t.action),O(t.date),L(t.terms),U(t.taxonomy);var e=D("publishpress-future/store").getFutureActionEnabled(),n=D("core/editor").isCleanNewPost();I("enabled",e),I("isCleanNewPost",n),e&&(n&&j(!0),X())}),[]);var W=[];return I("futureActionTerms",o),o&&o.length>0&&b&&"string"==typeof(W=d(o.map((function(t){return b[t]}))))&&(W=[]),React.createElement(i,{title:r.strings.panelTitle,icon:"calendar",initialOpen:u,className:"post-expirator-panel"},React.createElement(a,null,React.createElement(f,{label:r.strings.enablePostExpiration,checked:u,onChange:j})),u&&React.createElement(p,null,React.createElement(a,null,React.createElement(c,{currentDate:1e3*n,onChange:function(t){var e=new Date(t).getTime()/1e3;O(e),H({date:e})},__nextRemoveHelpButton:!0,is12Hour:r.is12hours,startOfWeek:r.startOfWeek})),React.createElement(l,{label:r.strings.action,value:e,options:r.actionsSelectOptions,onChange:function(t){C(t),H({action:t})}}),String(e).includes("category")&&(N&&React.createElement(p,null,r.strings.loading+" ("+s+")",React.createElement(T,null))||g(E(y))&&React.createElement("p",null,React.createElement("i",{className:"dashicons dashicons-warning"})," ",r.strings.noTermsFound)||React.createElement(m,{label:r.strings.terms+" ("+s+")",value:W,suggestions:Object.keys(y),onChange:function(t){t=t.map((function(t){return y[t].id})),L(t),H({terms:t})},maxSuggestions:10}))))}})}(window.wp,window.postExpiratorPanelConfig)})();
//# sourceMappingURL=gutenberg-panel.js.map